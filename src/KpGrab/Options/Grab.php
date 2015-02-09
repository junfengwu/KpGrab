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

/**
 * Class Grab
 * @package KpGrab\Options
 */
class Grab extends AbstractOptions
{
    /**
     *
     */
    const CONFIG_KEY = 'kp_grab';

    /**
     * @var \Zend\Http\Client\Adapter\AdapterInterface
     */
    protected $httpAdapter;

    /**
     * @var array
     */
    protected $httpAdapterOptions;

    /**
     * @var int
     */
    protected $consoleErrorMessageColor;

    /**
     * @var int
     */
    protected $maxReconnectionCount;

    /**
     * @var int
     */
    protected $xdebugMaxNestingLevel;

    /**
     * @var string
     */
    protected $defaultSaveDir;

    /**
     * @var array
     */
    protected $grabAllowPageSuffix;

    /**
     * @var array
     */
    protected $grabAllowStaticSuffix;

    /**
     * @var bool
     */
    protected $outputError;

    /**
     * @var string
     */
    protected $outputErrorFilename;

    /**
     * @var bool
     */
    protected $showMessage;

    /**
     * @return string
     */
    public function getOutputErrorFilename()
    {
        return $this->outputErrorFilename;
    }

    /**
     * @param $outputErrorFilename
     */
    public function setOutputErrorFilename($outputErrorFilename)
    {
        $this->outputErrorFilename = $outputErrorFilename;
    }

    /**
     * @return bool
     */
    public function getOutputError()
    {
        return $this->outputError;
    }

    /**
     * @param $outputError
     */
    public function setOutputError($outputError)
    {
        $this->outputError = $outputError;
    }

    /**
     * @return array
     */
    public function getGrabAllowStaticSuffix()
    {
        return $this->grabAllowStaticSuffix;
    }

    /**
     * @param $grabAllowStaticSuffix
     */
    public function setGrabAllowStaticSuffix($grabAllowStaticSuffix)
    {
        $this->grabAllowStaticSuffix = $grabAllowStaticSuffix;
    }

    /**
     * @return array
     */
    public function getGrabAllowPageSuffix()
    {
        return $this->grabAllowPageSuffix;
    }

    /**
     * @param $grabAllowPageSuffix
     */
    public function setGrabAllowPageSuffix($grabAllowPageSuffix)
    {
        $this->grabAllowPageSuffix = $grabAllowPageSuffix;
    }


    /**
     * @return string
     */
    public function getDefaultSaveDir()
    {
        return $this->defaultSaveDir;
    }

    /**
     * @param $defaultSaveDir
     */
    public function setDefaultSaveDir($defaultSaveDir)
    {
        $this->defaultSaveDir = $defaultSaveDir;
    }

    /**
     * @return int
     */
    public function getXdebugMaxNestingLevel()
    {
        return $this->xdebugMaxNestingLevel;
    }


    /**
     * @param $xdebugMaxNestingLevel
     */
    public function setXdebugMaxNestingLevel($xdebugMaxNestingLevel)
    {
        $this->xdebugMaxNestingLevel = $xdebugMaxNestingLevel;
    }

    /**
     * @return int
     */
    public function getMaxReconnectionCount()
    {
        return $this->maxReconnectionCount;
    }

    /**
     * @param $maxReconnectionCount
     * @return $this
     */
    public function setMaxReconnectionCount($maxReconnectionCount)
    {
        $this->maxReconnectionCount = $maxReconnectionCount;
        return $this;
    }

    /**
     * @return bool
     */
    public function getShowMessage()
    {
        return $this->showMessage;
    }

    /**
     * @param $showMessage
     */
    public function setShowMessage($showMessage)
    {
        $this->showMessage = $showMessage;
    }


    /**
     * @return int
     */
    public function getConsoleErrorMessageColor()
    {
        return $this->consoleErrorMessageColor;
    }

    /**
     * @param $consoleErrorMessageColor
     */
    public function setConsoleErrorMessageColor($consoleErrorMessageColor)
    {
        $this->consoleErrorMessageColor = $consoleErrorMessageColor;
    }

    /**
     * @return \Zend\Http\Client\Adapter\AdapterInterface
     */
    public function getHttpAdapter()
    {
        return $this->httpAdapter;
    }

    /**
     * @param $httpAdapter
     */
    public function setHttpAdapter($httpAdapter)
    {
        $this->httpAdapter = $httpAdapter;
    }

    /**
     * @return array
     */
    public function getHttpAdapterOptions()
    {
        return $this->httpAdapterOptions;
    }


    /**
     * @param $httpAdapterOptions
     */
    public function setHttpAdapterOptions($httpAdapterOptions)
    {
        $this->httpAdapterOptions = $httpAdapterOptions;
    }


}