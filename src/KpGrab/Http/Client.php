<?php
/**
 * Kittencup
 *
 * @date 2015 15/2/9 上午10:13
 * @copyright Copyright (c) 2014-2015 Kittencup. (http://www.kittencup.com)
 * @license   http://kittencup.com
 */

namespace KpGrab\Http;

use KpGrab\Options\GrabOptionsAwareInterface;
use KpGrab\Options\GrabOptionsAwareTrait;
use KpGrab\Result\Grab;
use KpGrab\Result\MessageInterface;
use Zend\Http\Client as ZendClient;
use Zend\Http\Client\Adapter\Exception\RuntimeException;
use KpGrab\Exception\RuntimeException as KpGrabRuntimeException;

class Client extends ZendClient implements GrabOptionsAwareInterface
{
    use GrabOptionsAwareTrait;

    protected $grabResult;
    protected $reconnectionCount = 1;

    public function setGrabResult(Grab $grabResult)
    {
        $this->grabResult = $grabResult;
        return $this;
    }

    public function canReconnectionSend($eventName)
    {
        $response = false;
        try {
            $response = parent::send();
        } catch (RuntimeException $e) {
            if ($this->reconnectionCount <= $this->grabOptions->getMaxReconnectionCount()) {
                $this->grabResult->setMessage(sprintf(MessageInterface::ERROR_RECONNECT_MESSAGE, $this->getUri(), $this->reconnectionCount++), $eventName);
                return $this->canReconnectionSend($eventName);
            } else {
                $this->grabResult->setMessage(new KpGrabRuntimeException(sprintf(MessageInterface::ERROR_CONNECT_FAIL_MESSAGE, $this->getUri(), $this->reconnectionCount++)), $eventName);
            }
        }

        $this->reconnectionCount = 1;

        return $response;
    }
}