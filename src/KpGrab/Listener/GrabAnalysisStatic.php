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
use Zend\EventManager\EventManagerAwareInterface;
use Zend\EventManager\EventManagerAwareTrait;
use Zend\EventManager\EventManagerInterface;
use Zend\EventManager\ListenerAggregateInterface;
use Zend\EventManager\ListenerAggregateTrait;


/**
 * Class GrabAnalysisStatic
 * @package KpGrab\Listener
 */
class GrabAnalysisStatic implements ListenerAggregateInterface, EventManagerAwareInterface
{
    use ListenerAggregateTrait;
    use EventManagerAwareTrait;

    /**
     * @var array
     */
    protected $hasUrlElements = [
        ['element' => 'link', 'attribute' => 'href'],
        ['element' => 'script', 'attribute' => 'src'],
        ['element' => 'img', 'attribute' => 'src']
    ];

    /**
     * @var array
     */
    protected $analyzedStaticUrl = [];

    /**
     * @param EventManagerInterface $events
     */
    public function attach(EventManagerInterface $events)
    {
        $this->listeners[] = $events->getSharedManager()->attach('*', GrabEvent::GRAB_ANALYSIS_STATIC, [$this, 'runAnalysis']);
    }


    /**
     * @param GrabEvent $event
     */
    public function runAnalysis(GrabEvent $event)
    {
        $grabHttpClient = $event->getGrabHttpClient();
        $grabResult = $event->getGrabResult();
        $grabOptions = $event->getGrabOptions();

        $readyAnalyzedStaticPageUrl = $grabResult->getGrabPageUrl();

        while (count($readyAnalyzedStaticPageUrl) > 0) {

            $url = array_shift($readyAnalyzedStaticPageUrl);

            $this->events->trigger(GrabEvent::GRAB_ANALYSIS_STATIC_PRE, $event->setParam('url', $url));

            if (!$response = $grabHttpClient->setUri($url)->canReconnectionSend($event->getName())) {
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

            $this->events->trigger(GrabEvent::GRAB_ANALYSIS_STATIC_POST, $event);

        }

        $grabResult->setGrabStaticUrl($this->analyzedStaticUrl);

        $this->events->trigger(GrabEvent::GRAB_ANALYSIS_STATIC_SUCCESS, $event);
    }

}