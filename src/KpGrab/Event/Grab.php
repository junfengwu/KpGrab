<?php
/**
 * Kittencup
 *
 * @date 2015 15/2/8 上午10:10
 * @copyright Copyright (c) 2014-2015 Kittencup. (http://www.kittencup.com)
 * @license   http://kittencup.com
 */

namespace KpGrab\Event;

use Zend\Console\ColorInterface;
use Zend\EventManager\Event;
use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\ServiceManager\ServiceLocatorAwareTrait;
use Zend\Console\Adapter\AdapterInterface as ConsoleAdapterInterface;
use Zend\Stdlib\AbstractOptions;

/**
 * Class Grab
 * @package KpGrab\Event
 */
class   Grab extends Event implements ServiceLocatorAwareInterface
{

    use ServiceLocatorAwareTrait;

    const ANALYSIS_SITE_PAGE = 'analysis.site.page';
    /**
     * @todo 还未使用该事件
     */
    const ANALYSIS_SITE_PAGE_POST = 'analysis.site.page.post';


    const ANALYSIS_SITE_STATIC = 'analysis.site.static';

    /**
     * @var array
     */
    protected $messages;
    /**
     * @var \Zend\Console\Request|\Zend\Http\PhpEnvironment\Request
     */
    protected $request;

    /**
     * @var \Zend\Console\Adapter\AdapterInterface|false;
     */
    protected $console;

    /**
     * @var \Zend\Http\Client;
     */
    protected $httpClient;

    /**
     * @var \KpGrab\Options\Grab
     */
    protected $grabOptions;

    /**
     * @var \Zend\Uri\Http
     */
    protected $origUri;



    /**
     * @return array
     */
    public function getMessages()
    {
        return $this->message;
    }

    /**
     * @return array
     */
    public function setMessages($messages)
    {
        $this->messages = $messages;
        return $this;
    }

    public function setMessage($message, $eventName, $color = ColorInterface::WHITE)
    {
        if ($color !== ColorInterface::BLACK || ($color === ColorInterface::WHITE && $this->grabOptions->getShowMessage())) {
            if ($this->console) {
                $this->console->writeLine($message, $color);
            } else {
                // @todo 浏览器
            }
        }

        $this->messages[$eventName] = ['color' => $color, 'message' => $message, 'time' => time()];

        return $this;

    }

    /**
     * @return array
     */
    public function getAnalyzedPageUrl()
    {
        return $this->analyzedPageUrl;
    }

    /**
     * @param array $analyzedPageUrl
     */
    public function setAnalyzedPageUrl($analyzedPageUrl)
    {
        $this->analyzedPageUrl = $analyzedPageUrl;
    }

    /**
     * @var array
     */
    protected $analyzedPageUrl;

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
     * @return false|\Zend\Console\Adapter\AdapterInterface
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
        if (!$console instanceof ConsoleAdapterInterface) {
            $console = false;
        }
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
     * @return \KpGrab\Options\Grab
     */
    public function getGrabOptions()
    {
        return $this->grabOptions;
    }


    /**
     * @param AbstractOptions $grabOptions
     * @return $this
     */
    public function setGrabOptions(AbstractOptions $grabOptions)
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


    public function analysisAbsoluteUrl($url)
    {

        $url = rtrim($url, '/');

        $urlInfo = parse_url($url);

        if (!array_key_exists('path', $urlInfo)) {
            $urlInfo['path'] = '/';
        } else {
            $pathInfo = pathinfo($urlInfo['path']);

            if (array_key_exists('extension', $pathInfo)) {
                $urlInfo['path'] = str_replace($pathInfo['filename'] . '.' . $pathInfo['extension'], '', $urlInfo['path']);
            }

            $urlInfo['path'] = rtrim($urlInfo['path'], '/');

        }


        return $urlInfo;

    }


}