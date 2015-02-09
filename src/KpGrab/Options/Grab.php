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

class Grab extends AbstractOptions
{
    const CONFIG_KEY = 'kp_grab';

    /**
     * @var
     */
    protected $httpAdapter;
    /**
     * @var
     */
    protected $httpAdapterOptions;
    /**
     * @var
     */
    protected $consoleErrorMessageColor;

    protected $maxReconnectionCount;

    protected $xdebugMaxNestingLevel;

    protected $defaultSaveDir;

    protected $grabAllowPageSuffix;

    protected $grabAllowStaticSuffix;

    protected $outputError;

    protected $outputErrorFilename;

    /**
     * @return mixed
     */
    public function getOutputErrorFilename()
    {
        return $this->outputErrorFilename;
    }

    /**
     * @param mixed $outputErrorFilename
     */
    public function setOutputErrorFilename($outputErrorFilename)
    {
        $this->outputErrorFilename = $outputErrorFilename;
    }

    /**
     * @return mixed
     */
    public function getOutputError()
    {
        return $this->outputError;
    }

    /**
     * @param mixed $outputError
     */
    public function setOutputError($outputError)
    {
        $this->outputError = $outputError;
    }


    /**
     * @return mixed
     */
    public function getGrabAllowStaticSuffix()
    {
        return $this->grabAllowStaticSuffix;
    }

    /**
     * @param mixed $grabAllowStaticSuffix
     */
    public function setGrabAllowStaticSuffix($grabAllowStaticSuffix)
    {
        $this->grabAllowStaticSuffix = $grabAllowStaticSuffix;
    }

    /**
     * @return mixed
     */
    public function getGrabAllowPageSuffix()
    {
        return $this->grabAllowPageSuffix;
    }

    /**
     * @param mixed $grabAllowPageSuffix
     */
    public function setGrabAllowPageSuffix($grabAllowPageSuffix)
    {
        $this->grabAllowPageSuffix = $grabAllowPageSuffix;
    }

    /**
     * @return mixed
     */
    public function getDefaultSaveDir()
    {
        return $this->defaultSaveDir;
    }

    /**
     * @param mixed $defaultSaveDir
     */
    public function setDefaultSaveDir($defaultSaveDir)
    {
        $this->defaultSaveDir = $defaultSaveDir;
    }

    /**
     * @return mixed
     */
    public function getXdebugMaxNestingLevel()
    {
        return $this->xdebugMaxNestingLevel;
    }


    public function setXdebugMaxNestingLevel($xdebugMaxNestingLevel)
    {
        $this->xdebugMaxNestingLevel = $xdebugMaxNestingLevel;
    }

    /**
     * @return mixed
     */
    public function getMaxReconnectionCount()
    {
        return $this->maxReconnectionCount;
    }

    public function setMaxReconnectionCount($maxReconnectionCount)
    {
        $this->maxReconnectionCount = $maxReconnectionCount;
        return $this;
    }

    /**
     * @return boolean
     */
    public function getShowMessage()
    {
        return $this->showMessage;
    }

    /**
     * @param boolean $showMessage
     */
    public function setShowMessage($showMessage)
    {
        $this->showMessage = $showMessage;
    }

    /**
     * @var boolean
     */
    protected $showMessage;


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