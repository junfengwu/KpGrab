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
use KpGrab\Result\MessageInterface;
use KpGrab\Tools\Uri;
use KpGrab\Exception\RuntimeException;
use Zend\Dom\Document;
use Zend\Http\Response;
use Zend\EventManager\EventManagerInterface;
use Zend\EventManager\ListenerAggregateInterface;
use Zend\EventManager\ListenerAggregateTrait;


/**
 * Class GrabAnalysisPage
 * @package KpGrab\Listener
 */
class GrabAnalysisPage implements ListenerAggregateInterface
{
    use ListenerAggregateTrait;

    /**
     * @var array
     */
    protected $alreadyAnalyzedPageUrl = [];
    /**
     * @var array
     */
    protected $readyAnalyzedPageUrl = [];
    /**
     * @var array
     */
    protected $errorAnalyzedPageUrl = [];

    /**
     * @var array
     */
    protected $hasUrlElements = [
        ['element' => 'a', 'attribute' => 'href'],
        ['element' => 'form', 'attribute' => 'action']
    ];


    /**
     * @param EventManagerInterface $events
     */
    public function attach(EventManagerInterface $events)
    {
        $this->listeners[] = $events->getSharedManager()->attach('*', GrabEvent::GRAB_ANALYSIS_PAGE, [$this, 'runAnalysis']);
    }

    /**
     * @param GrabEvent $event
     */
    public function runAnalysis(GrabEvent $event)
    {

        $grabHttpClient = $event->getGrabHttpClient();
        $grabResult = $event->getGrabResult();
        $grabOptions = $event->getGrabOptions();

        $this->readyAnalyzedPageUrl[] = $event->getOrigUri()->toString();

        while (count($this->readyAnalyzedPageUrl) > 0) {

            $url = array_shift($this->readyAnalyzedPageUrl);

            $response = $grabHttpClient->setUri($url)->canReconnectionSend($event->getName());

            if (!$response) {
                $this->errorAnalyzedPageUrl[] = $url;
                continue;
            }

            if ($response->getStatusCode() !== Response::STATUS_CODE_200) {
                $this->errorAnalyzedPageUrl[] = $url;
                $grabResult->setMessage(new RuntimeException(sprintf(MessageInterface::ERROR_CONNECT_CODE_MESSAGE, $url, $response->getStatusCode())), $event->getName());
                continue;
            }

            $this->alreadyAnalyzedPageUrl[] = $url;

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

                $findUrl = Uri::getRealUrl($findUrl);
                $findUrlInfo = Uri::parseAbsoluteUrl($findUrl);

                if (!isset($findUrlInfo['extension'])) {
                    continue;
                }

                if (!in_array($findUrlInfo['extension'], $grabOptions->getGrabAllowPageSuffix())) {
                    continue;
                }

                if ($event->getOrigUri()->getHost() !== $findUrlInfo['host']) {
                    continue;
                }

                if (!in_array($findUrl, $this->readyAnalyzedPageUrl) &&
                    !in_array($findUrl, $this->alreadyAnalyzedPageUrl) &&
                    !in_array($findUrl, $this->errorAnalyzedPageUrl)
                ) {
                    $this->readyAnalyzedPageUrl[] = $findUrl;
                }
            }
        }

        $grabResult->setGrabPageUrl($this->alreadyAnalyzedPageUrl);

    }

}