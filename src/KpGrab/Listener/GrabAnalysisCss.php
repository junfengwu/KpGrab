<?php
/**
 * Kittencup
 *
 * @date 2015 15/2/8 上午10:37
 * @copyright Copyright (c) 2014-2015 Kittencup. (http://www.kittencup.com)
 * @license   http://kittencup.com
 */

namespace KpGrab\Listener;

use KpGrab\Event\Grab as GrabEvent;
use Zend\Dom\Document;
use Zend\EventManager\EventInterface;
use Zend\EventManager\EventManagerInterface;
use Zend\EventManager\ListenerAggregateInterface;
use Zend\EventManager\ListenerAggregateTrait;
use Zend\Http\Response;
use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\ServiceManager\ServiceLocatorAwareTrait;

use KpGrab\Tools\Uri;

class GrabAnalysisCss implements ListenerAggregateInterface, ServiceLocatorAwareInterface
{
    use ServiceLocatorAwareTrait;
    use ListenerAggregateTrait;

    const CSS_SUFFIX = 'css';

    protected $errorAnalyzedCssUrl;


    public function attach(EventManagerInterface $events)
    {
        $this->listeners[] = $events->getSharedManager()->attach('*', GrabEvent::GRAB_ANALYSIS_CSS, [$this, 'runAnalysisCss']);
    }

    public function runAnalysisCss(GrabEvent $event)
    {
        $grabHttpClient = $event->getGrabHttpClient();
        $grabOptions = $event->getGrabOptions();
        $grabResult = $event->getGrabResult();

        $analyzedStaticUrl = $grabResult->getGrabStaticUrl();
        $siteCssList = $grabResult->getGrabStaticUrl();

        while (count($siteCssList) > 0) {

            $url = array_shift($siteCssList);

            $urlInfo = Uri::parseAbsoluteUrl($url);

            if (!isset($urlInfo['extension']) || $urlInfo['extension'] !== Static::CSS_SUFFIX) {
                continue;
            }

            $response = $grabHttpClient->setUri($url)->canReconnectionSend($event->getName());

            if (!$response || $response->getStatusCode() !== Response::STATUS_CODE_200) {
                continue;
            }

            $cssInsideUrl = $this->getCssUrl($response->getContent());

            $urlInfo = Uri::parseAbsoluteUrl($url);

            foreach ($cssInsideUrl as $findUrl) {

                if (!Uri::isAbsoluteUrl($findUrl)) {
                    $findUrl = $urlInfo['scheme'] . '://' . $urlInfo['host'] . $urlInfo['path'] . '/' . $findUrl;
                }

                $findUrlInfo = Uri::parseAbsoluteUrl($findUrl);

                if (!isset($findUrlInfo['extension'])) {
                    continue;
                }

                if (!in_array($findUrlInfo['extension'], $grabOptions->getGrabAllowStaticSuffix())) {
                    continue;
                }

                if ($event->getOrigUri()->getHost() !== $findUrlInfo['host']) {
                    continue;
                }

                $findUrl = Uri::getRealUrl($findUrl);

                if (!in_array($findUrl, $analyzedStaticUrl)
                ) {
                    $analyzedStaticUrl[] = $findUrl;
                    $siteCssList[] = $findUrl;
                }
            }

        }

        $grabResult->setGrabStaticUrl($analyzedStaticUrl);
    }

    public function getCssUrl($text)
    {
        $urls = [];

        $url_pattern = '(([^\\\\\'", \(\)]*(\\\\.)?)+)';
        $urlfunc_pattern = 'url\(\s*[\'"]?' . $url_pattern . '[\'"]?\s*\)';
        $pattern = '/(' .
            '(@import\s*[\'"]' . $url_pattern . '[\'"])' .
            '|(@import\s*' . $urlfunc_pattern . ')' .
            '|(' . $urlfunc_pattern . ')' . ')/iu';
        if (!preg_match_all($pattern, $text, $matches))
            return $urls;


        $urls['import'] = [];
        $urls['image'] = [];
        // @import '...'
        // @import "..."
        foreach ($matches[3] as $match)
            if (!empty($match))
                $urls['import'][] =
                    preg_replace('/\\\\(.)/u', '\\1', $match);

        // @import url(...)
        // @import url('...')
        // @import url("...")
        foreach ($matches[7] as $match)
            if (!empty($match))
                $urls['import'][] =
                    preg_replace('/\\\\(.)/u', '\\1', $match);

        // url(...)
        // url('...')
        // url("...")
        foreach ($matches[11] as $match)
            if (!empty($match))
                $urls['image'][] =
                    preg_replace('/\\\\(.)/u', '\\1', $match);

        return array_merge($urls['image'], $urls['import']);
    }

}