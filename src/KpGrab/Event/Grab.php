<?php
/**
 * Kittencup
 *
 * @date 2015 15/2/8 上午10:10
 * @copyright Copyright (c) 2014-2015 Kittencup. (http://www.kittencup.com)
 * @license   http://kittencup.com
 */

namespace KpGrab\Event;

use Zend\EventManager\Event;
use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\ServiceManager\ServiceLocatorAwareTrait;

/**
 * Class Grab
 * @package KpGrab\Event
 */
class Grab extends Event implements ServiceLocatorAwareInterface{

    use ServiceLocatorAwareTrait;

    /**
     * @var \Zend\Console\Request | \Zend\Http\PhpEnvironment\Request
     */
    protected $request;

    /**
     * @var \Zend\Console\Adapter\AdapterInterface | null;
     */
    protected $console;

    /**
     * @var \Zend\Http\Client;
     */
    protected $httpClient;

    /**
     * @var \KpGrab\Options\KpGrab
     */
    protected $grabOptions;

    /**
     * @var \Zend\Uri\Http
     */
    protected $origUri;

    /**
     * @var \Zend\Uri\Http
     */
    protected $uri;

    /**
     * @return \Zend\Console\Request|\Zend\Http\PhpEnvironment\Request
     */
    public function getRequest()
    {
        return $this->request;
    }

    /**
     * @param $request
     * @return $this
     */
    public function setRequest($request)
    {
        $this->request = $request;
        return $this;
    }

    /**
     * @return null|\Zend\Console\Adapter\AdapterInterface
     */
    public function getConsole()
    {
        return $this->console;
    }


    /**
     * @param $console
     * @return $this
     */
    public function setConsole($console)
    {
        $this->console = $console;
        return $this;
    }


    /**
     * @return \Zend\Http\Client
     */
    public function getHttpClient()
    {
        return $this->httpClient;
    }

    /**
     * @param $httpClient
     * @return $this
     */
    public function setHttpClient($httpClient)
    {
        $this->httpClient = $httpClient;
        return $this;
    }


    /**
     * @return \KpGrab\Options\KpGrab
     */
    public function getGrabOptions()
    {
        return $this->grabOptions;
    }

    /**
     * @param $grabOptions
     * @return $this
     */
    public function setGrabOptions($grabOptions)
    {
        $this->grabOptions = $grabOptions;
        return $this;
    }

    /**
     * @return \Zend\Uri\Http
     */
    public function getOrigUri()
    {
        return $this->origUri;
    }


    /**
     * @param $origUri
     * @return $this
     */
    public function setOrigUri($origUri)
    {
        $this->origUri = $origUri;
        return $this;
    }

    /**
     * @return \Zend\Uri\Http
     */
    public function getUri()
    {
        return $this->uri;
    }


    /**
     * @param $uri
     * @return $this
     */
    public function setUri($uri)
    {
        $this->uri = $uri;
        return $this;
    }



}