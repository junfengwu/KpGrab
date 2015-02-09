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
use Zend\Dom\Document;
use Zend\EventManager\EventManagerInterface;
use Zend\EventManager\ListenerAggregateInterface;
use Zend\EventManager\ListenerAggregateTrait;
use Zend\Http\Response;


class GrabPost implements ListenerAggregateInterface
{
    use ListenerAggregateTrait;

    public function attach(EventManagerInterface $events)
    {
        $this->listeners[] = $events->getSharedManager()->attach('*', GrabEvent::GRAB_POST, [$this, 'outputError']);
    }

    public function outputError(GrabEvent $event)
    {
        $grabResult = $event->getGrabResult();

        $request = $event->getRequest();
        $saveDir = $request->getParam('save-dir');
        $saveName = $request->getParam('save-name');

        $grabOptions = $event->getGrabOptions();

        $errorFileSaveDir = $saveDir . '/' . $saveName;

        if (!is_dir($errorFileSaveDir)) {
            mkdir($errorFileSaveDir, 0777, true);
            chmod($errorFileSaveDir, 0777);
        }

        $errorMessages = $grabResult->getMessages();

        $errorMessageStr = '';

        if (isset($errorMessages[MessageInterface::ERROR_MESSAGE])) {
            foreach ($errorMessages[MessageInterface::ERROR_MESSAGE] as $errorMessage) {
                $errorMessageStr .= $errorMessage['event'] . '-' . $errorMessage['message'] . '-' . date('Y-m-d:H:i:s', $errorMessage['time']) . PHP_EOL;
            }
        }
        file_put_contents($errorFileSaveDir . '/' . $grabOptions->getOutputErrorFilename(), $errorMessageStr);
    }

}