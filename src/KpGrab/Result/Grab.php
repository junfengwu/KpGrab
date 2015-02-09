<?php
/**
 * Kittencup
 *
 * @date 2015 15/2/9 上午9:44
 * @copyright Copyright (c) 2014-2015 Kittencup. (http://www.kittencup.com)
 * @license   http://kittencup.com
 */

namespace KpGrab\Result;

use KpGrab\Exception\ExceptionInterface;
use Zend\Console\Adapter\AdapterInterface;
use KpGrab\Options\Grab as GrabOptions;

class Grab implements MessageInterface
{
    protected $messages = [];
    protected $grabPageUrl = [];
    protected $grabStaticUrl = [];


    /**
     * @var \KpGrab\Options\Grab
     */
    protected $grabOptions;
    protected $console;


    /**
     * @return array
     */
    public function getGrabStaticUrl()
    {
        return $this->grabStaticUrl;
    }

    /**
     * @param array $grabStaticUrl
     */
    public function setGrabStaticUrl($grabStaticUrl)
    {
        $this->grabStaticUrl = $grabStaticUrl;
    }

    /**
     * @return array
     */
    public function getGrabPageUrl()
    {
        return $this->grabPageUrl;
    }

    /**
     * @param array $grabPageUrl
     */
    public function setGrabPageUrl($grabPageUrl)
    {
        $this->grabPageUrl = $grabPageUrl;
    }



    public function getGrabOptions()
    {
        return $this->grabOptions;
    }

    public function setGrabOptions(GrabOptions $grabOptions)
    {
        $this->grabOptions = $grabOptions;
        return $this;
    }

    public function setConsole(AdapterInterface $console)
    {
        $this->console = $console;
        return $this;
    }

    public function getConsole()
    {
        return $this->console;
    }

    public function setMessage($message, $eventName, $isExit = false)
    {

        $key = $message instanceof ExceptionInterface ? Static::ERROR_MESSAGE : Static::NORMAL_MESSAGE;

        if ($key === Static::ERROR_MESSAGE) {
            $color = $this->grabOptions->getConsoleErrorMessageColor();
            $message = $message->getMessage();
        } else {
            $color = Static::DEFAULT_CONSOLE_COLOR;
        }

        $this->messages[$key][] = ['event' => $eventName,'message' => $message, 'time' => time()];

        if($key === Static::ERROR_MESSAGE || $this->grabOptions->getShowMessage()) {
            $this->console->writeLine($message, $color);
        }

        if ($isExit) {
            exit();
        }

        return $this;
    }

    public function getMessages()
    {
        return $this->messages;
    }


}