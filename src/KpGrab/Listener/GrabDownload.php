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
use KpGrab\Tools\Html;
use Zend\Dom\Document;
use Zend\EventManager\EventManagerAwareInterface;
use Zend\EventManager\EventManagerAwareTrait;
use Zend\EventManager\EventManagerInterface;
use Zend\EventManager\ListenerAggregateInterface;
use Zend\EventManager\ListenerAggregateTrait;
use Zend\Http\Response;
use KpGrab\Tools\Uri;

/**
 * Class GrabDownload
 * @package KpGrab\Listener
 */
class GrabDownload implements ListenerAggregateInterface, EventManagerAwareInterface
{
    use ListenerAggregateTrait;
    use EventManagerAwareTrait;

    /**
     * @param EventManagerInterface $events
     */
    public function attach(EventManagerInterface $events)
    {
        $this->listeners[] = $events->getSharedManager()->attach('*', GrabEvent::GRAB_DOWNLOAD, [$this, 'runDownload']);
    }

    /**
     * @param GrabEvent $event
     */
    public function runDownload(GrabEvent $event)
    {

        $grabResult = $event->getGrabResult();
        $request = $event->getRequest();
        $grabHttpClient = $event->getGrabHttpClient();

        $saveDir = $request->getParam('save-dir');
        $saveName = $request->getParam('save-name');

        $downloadList = array_merge($grabResult->getGrabPageUrl(), $grabResult->getGrabStaticUrl());

        while (count($downloadList) > 0) {

            $url = array_shift($downloadList);

            $this->events->trigger(GrabEvent::GRAB_DOWNLOAD_PRE, $event->setParam('url', $url));

            if (!$response = $grabHttpClient->setUri($url)->canReconnectionSend($event->getName())) {
                continue;
            }

            $urlInfo = Uri::parseAbsoluteUrl($url);

            $downloadSaveDir = $saveDir . '/' . $saveName . '/' . $urlInfo['path'];

            if (!is_dir($downloadSaveDir)) {
                mkdir($downloadSaveDir, 0777, true);
                chmod($downloadSaveDir, 0777);
            }

            $fileName = $urlInfo['filename'] . '.' . $urlInfo['extension'];

            $content = $response->getContent();

            /**
             * @todo need format html,css,js
             */
//            if (in_array($urlInfo['extension'], $grabOptions->getGrabAllowPageSuffix())) {
//                $content = Html::format($content);
//            }

            file_put_contents($downloadSaveDir . '/' . $fileName, $content);

            $this->events->trigger(GrabEvent::GRAB_DOWNLOAD_POST, $event);
        }

        $this->events->trigger(GrabEvent::GRAB_DOWNLOAD_SUCCESS, $event);
    }

}