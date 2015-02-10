<?php
/**
 * Kittencup
 *
 * @date 2015 15/2/9 上午10:13
 * @copyright Copyright (c) 2014-2015 Kittencup. (http://www.kittencup.com)
 * @license   http://kittencup.com
 */

namespace KpGrab\Http;

use KpGrab\Options\GrabTrait;
use KpGrab\Result\Grab;
use KpGrab\Result\MessageInterface;
use Zend\Http\Client as ZendClient;
use Zend\Http\Client\Adapter\Exception\RuntimeException as ZendHttpClientAdapterRuntimeException;
use KpGrab\Exception\RuntimeException;
use Zend\Http\Exception\InvalidArgumentException as ZendHttpInvalidArgumentException;
use KpGrab\Exception\InvalidArgumentException;
use Zend\Http\Response;

/**
 * Class Client
 * @package KpGrab\Http
 */
class Client extends ZendClient
{
    use GrabTrait;

    /**
     * @var Grab;
     */
    protected $grabResult;
    /**
     * @var int
     */
    protected $reconnectionCount = 1;

    /**
     * @param Grab $grabResult
     * @return $this
     */
    public function setGrabResult(Grab $grabResult)
    {
        $this->grabResult = $grabResult;
        return $this;
    }

    /**
     * @param $eventName
     * @return bool|\Zend\Http\Response
     */
    public function canReconnectionSend($eventName)
    {

        $response = false;
        try {
            $response = parent::send();
        } catch (ZendHttpClientAdapterRuntimeException $e) {
            if ($this->reconnectionCount <= $this->grabOptions->getMaxReconnectionCount()) {
                $this->grabResult->setMessage(sprintf(MessageInterface::ERROR_RECONNECT_MESSAGE, $this->getUri(), $this->reconnectionCount++), $eventName);
                return $this->canReconnectionSend($eventName);
            } else {
                $this->grabResult->setMessage(new RuntimeException(sprintf(MessageInterface::ERROR_CONNECT_FAIL_MESSAGE, $this->getUri(), $this->reconnectionCount)), $eventName);
            }
        } catch (ZendHttpInvalidArgumentException $e) {
            $this->grabResult->setMessage(new InvalidArgumentException(sprintf(MessageInterface::ERROR_UNKNOWN_MESSAGE, $this->getUri(), $e->getMessage())), $eventName);
        }

        $this->reconnectionCount = 1;

        if ($response->getStatusCode() !== Response::STATUS_CODE_200) {
            $this->grabResult->setMessage(new RuntimeException(sprintf(MessageInterface::ERROR_CONNECT_CODE_MESSAGE, $this->getUri(), $response->getStatusCode())), $eventName);
            return false;
        }

        return $response;
    }
}