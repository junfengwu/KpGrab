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
use KpGrab\Tools\Uri;
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


class GrabAnalysisStatic implements ListenerAggregateInterface, ServiceLocatorAwareInterface, EventManagerAwareInterface
{
    use ServiceLocatorAwareTrait;
    use ListenerAggregateTrait;
    use EventManagerAwareTrait;

    protected $hasUrlElements = [
        ['element' => 'link', 'attribute' => 'href'],
        ['element' => 'script', 'attribute' => 'src'],
        ['element' => 'img', 'attribute' => 'src']
    ];

    protected $analyzedStaticUrl = [];

    public function attach(EventManagerInterface $events)
    {
        $this->listeners[] = $events->getSharedManager()->attach('*', GrabEvent::GRAB_ANALYSIS_STATIC, [$this, 'runAnalysis']);
    }


    public function runAnalysis(EventInterface $event)
    {
        /* @var $event \KpGrab\Event\Grab */
        $grabHttpClient = $event->getGrabHttpClient();
        $grabResult = $event->getGrabResult();
        $grabOptions = $event->getGrabOptions();


        $readyAnalyzedStaticPageUrl = $grabResult->getGrabPageUrl();

        while (count($readyAnalyzedStaticPageUrl) > 0) {

            $url = array_shift($readyAnalyzedStaticPageUrl);

            $response = $grabHttpClient->setUri($url)->canReconnectionSend($event->getName());

            if (!$response) {
                continue;
            }

            if ($response->getStatusCode() !== Response::STATUS_CODE_200) {
                continue;
            }

            $document = new Document($response->getContent());
            $dom = new Document\Query();

            $findUrlList = [];

            foreach ($this->hasUrlElements as $element) {
                foreach ($dom->execute($element['element'], $document, Document\Query::TYPE_CSS) as $node) {
                    $findUrl = $node->getAttribute($element['attribute']);
                    if ($findUrl) {
                        $findUrlList[] = $findUrl;
                    }
                }
            }

            $urlInfo = Uri::parseAbsoluteUrl($url);

            foreach ($findUrlList as $findUrl) {

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

                if (!in_array($findUrl, $this->analyzedStaticUrl)
                ) {
                    $this->analyzedStaticUrl[] = $findUrl;
                }
            }

        }

        $grabResult->setGrabStaticUrl($this->analyzedStaticUrl);

    }

}