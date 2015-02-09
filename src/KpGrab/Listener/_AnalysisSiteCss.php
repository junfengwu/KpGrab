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
use KpGrab\Exception\InvalidArgumentException;
use Zend\Console\Request;
use Zend\Dom\Document;
use Zend\EventManager\EventInterface;
use Zend\EventManager\EventManagerAwareInterface;
use Zend\EventManager\EventManagerAwareTrait;
use Zend\EventManager\EventManagerInterface;
use Zend\EventManager\ListenerAggregateInterface;
use Zend\EventManager\ListenerAggregateTrait;
use Zend\Http\Response;
use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\ServiceManager\ServiceLocatorAwareTrait;

use Zend\Uri\UriFactory;
use Zend\Http\Client\Adapter\Exception\RuntimeException;
use Zend\Validator\Uri;

class AnalysisSiteCss implements ListenerAggregateInterface, ServiceLocatorAwareInterface
{
    use ServiceLocatorAwareTrait;
    use ListenerAggregateTrait;

    protected $errorAnalyzedCssUrl;

    public function attach(EventManagerInterface $events)
    {
        $this->listeners[] = $events->getSharedManager()->attach('*', GrabEvent::ANALYSIS_SITE_CSS, [$this, 'runAnalysisCss']);
    }

    public function runAnalysisCss(EventInterface $event)
    {
        /* @var $event \KpGrab\Event\Grab */
        $httpClient = $event->getHttpClient();
        $grabOptions = $event->getGrabOptions();

        $analyzedPageImage = $event->getAnalyzedPageImage();
        $analyzedPageCss = $event->getAnalyzedPageCss();

        $siteCssList = $event->getAnalyzedPageCss();

        while(count($siteCssList) > 0){

            $url = array_shift($siteCssList);

            try {
                $response = $httpClient->setUri($url)->send();
            } catch (RuntimeException $e) {
                $event->setMessage(sprintf(AnalysisSiteStatic::RECONNECTION_MESSAGE, $url , ++AnalysisSiteStatic::$reconnectionCount) , $event->getName() , $grabOptions->getConsoleErrorMessageColor());

                if(AnalysisSitePage::$reconnectionCount >= $grabOptions->getMaxReconnectionCount()){
                    AnalysisSitePage::$reconnectionCount = 0;
                    $this->errorAnalyzedCssUrl[] = $url;
                }else{
                    array_unshift($siteCssList, $url);
                }

                continue;
            }

            $urlInfo = $event->analysisAbsoluteUrl($url);

            $cssInsideUrl = $this->getCssUrl($response->getContent());

            $uriValidator = new Uri(['allowRelative' => false]);

            if(isset($cssInsideUrl['image'])) {
                foreach ($cssInsideUrl['image'] as $findUrl) {

                    if (!$uriValidator->isValid($findUrl)) {
                        $findUrl = $urlInfo['scheme'] . '://' . $urlInfo['host'] . $urlInfo['path'] . '/' . $findUrl;
                    }

                    if ($event->getOrigUri()->getHost() !== parse_url($findUrl, PHP_URL_HOST)) {
                        continue;
                    }

                    $findUrl = $event->getRealUrl($findUrl);

                    if (!in_array($findUrl, $analyzedPageImage)) {
                        $analyzedPageImage[] = $findUrl;
                    }
                }
            }

            if(isset($cssInsideUrl['import'])) {
                foreach ($cssInsideUrl['import'] as $findUrl) {
                    if (!$uriValidator->isValid($findUrl)) {
                        $findUrl = $urlInfo['scheme'] . '://' . $urlInfo['host'] . $urlInfo['path'] . '/' . $findUrl;
                    }

                    if ($event->getOrigUri()->getHost() !== parse_url($findUrl, PHP_URL_HOST)) {
                        continue;
                    }

                    $findUrl = $event->getRealUrl($findUrl);

                    if (!in_array($findUrl, $analyzedPageCss)) {
                        $analyzedPageCss[] = $findUrl;

                        // 找到新的css 本次while也要在遍历
                        $siteCssList[] = $findUrl;
                    }
                }
            }

            $event->setAnalyzedPageImage($analyzedPageImage);
            $event->setAnalyzedPageCss($analyzedPageCss);

        }


    }

    public function getCssUrl( $text )
    {
        $urls = array( );

        $url_pattern     = '(([^\\\\\'", \(\)]*(\\\\.)?)+)';
        $urlfunc_pattern = 'url\(\s*[\'"]?' . $url_pattern . '[\'"]?\s*\)';
        $pattern         = '/(' .
            '(@import\s*[\'"]' . $url_pattern     . '[\'"])' .
            '|(@import\s*'      . $urlfunc_pattern . ')'      .
            '|('                . $urlfunc_pattern . ')'      .  ')/iu';
        if ( !preg_match_all( $pattern, $text, $matches ) )
            return $urls;

        // @import '...'
        // @import "..."
        foreach ( $matches[3] as $match )
            if ( !empty($match) )
                $urls['import'][] =
                    preg_replace( '/\\\\(.)/u', '\\1', $match );

        // @import url(...)
        // @import url('...')
        // @import url("...")
        foreach ( $matches[7] as $match )
            if ( !empty($match) )
                $urls['import'][] =
                    preg_replace( '/\\\\(.)/u', '\\1', $match );

        // url(...)
        // url('...')
        // url("...")
        foreach ( $matches[11] as $match )
            if ( !empty($match) )
                $urls['image'][] =
                    preg_replace( '/\\\\(.)/u', '\\1', $match );

        return $urls;
    }

}