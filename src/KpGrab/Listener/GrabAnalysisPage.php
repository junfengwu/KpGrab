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
use Zend\Dom\Document;
use Zend\EventManager\EventInterface;
use Zend\EventManager\EventManagerInterface;
use Zend\EventManager\ListenerAggregateInterface;
use Zend\EventManager\ListenerAggregateTrait;
use Zend\Http\Response;
use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\ServiceManager\ServiceLocatorAwareTrait;

use KpGrab\Exception\RuntimeException;


class GrabAnalysisPage implements ListenerAggregateInterface, ServiceLocatorAwareInterface
{
    use ServiceLocatorAwareTrait;
    use ListenerAggregateTrait;

    protected $alreadyAnalyzedPageUrl = [];
    protected $readyAnalyzedPageUrl = [];
    protected $errorAnalyzedPageUrl = [];

    protected $hasUrlElements = [
        ['element' => 'a', 'attribute' => 'href'],
        ['element' => 'form', 'attribute' => 'action']
    ];


    public function attach(EventManagerInterface $events)
    {
        $this->listeners[] = $events->getSharedManager()->attach('*', GrabEvent::GRAB_ANALYSIS_PAGE, [$this, 'runAnalysis']);
    }

    public function runAnalysis(EventInterface $event)
    {
        /* @var $event \KpGrab\Event\Grab */

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

                $findUrl = Uri::getRealUrl($findUrl);

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