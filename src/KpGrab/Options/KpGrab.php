<?php
/**
 * Kittencup
 *
 * @date 2015 15/2/7 下午6:04
 * @copyright Copyright (c) 2014-2015 Kittencup. (http://www.kittencup.com)
 * @license   http://kittencup.com
 */
namespace KpGrab\Options;

use Zend\Stdlib\AbstractOptions;

class KpGrab extends AbstractOptions
{
    const CONFIG_KEY = 'kp_grab';

    protected $httpAdapter;
    protected $httpAdapterOptions;
    protected $consoleErrorMessageColor;
    protected $testUrl;

    /**
     * @return mixed
     */
    public function getTestUrl()
    {
        return $this->testUrl;
    }

    /**
     * @param mixed $testUrl
     */
    public function setTestUrl($testUrl)
    {
        $this->testUrl = $testUrl;
    }

    /**
     * @return mixed
     */
    public function getConsoleErrorMessageColor()
    {
        return $this->consoleErrorMessageColor;
    }

    /**
     * @param mixed $consoleErrorMessageColor
     */
    public function setConsoleErrorMessageColor($consoleErrorMessageColor)
    {
        $this->consoleErrorMessageColor = $consoleErrorMessageColor;
    }

    /**
     * @return mixed
     */
    public function getHttpAdapter()
    {
        return $this->httpAdapter;
    }

    /**
     * @param mixed $httpAdapter
     */
    public function setHttpAdapter($httpAdapter)
    {
        $this->httpAdapter = $httpAdapter;
    }

    /**
     * @return mixed
     */
    public function getHttpAdapterOptions()
    {
        return $this->httpAdapterOptions;
    }

    /**
     * @param mixed $httpAdapterOptions
     */
    public function setHttpAdapterOptions($httpAdapterOptions)
    {
        $this->httpAdapterOptions = $httpAdapterOptions;
    }


}