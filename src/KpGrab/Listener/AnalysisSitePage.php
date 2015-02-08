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
use KpGrab\Exception\InvalidArgumentException;
use Zend\Console\Request;
use Zend\Dom\Document;
use Zend\EventManager\EventInterface;
use Zend\EventManager\EventManagerInterface;
use Zend\EventManager\ListenerAggregateInterface;
use Zend\EventManager\ListenerAggregateTrait;
use Zend\Http\Response;
use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\ServiceManager\ServiceLocatorAwareTrait;

use Zend\Uri\UriFactory;
use Zend\Http\Client\Adapter\Exception\RuntimeException;
use Zend\Validator\Uri;

class AnalysisSitePage implements ListenerAggregateInterface, ServiceLocatorAwareInterface
{
    use ServiceLocatorAwareTrait;
    use ListenerAggregateTrait;

    const URI_ERROR_MESSAGE = '请输入一个正确的url参数';
    const START_ANALYSIS_MESSAGE = '开始分析[%s]页面';
    const ADD_ANALYSIS_MESSAGE = '新增准备分析页面[%s]';
    const RECONNECTION_MESSAGE = '[%s]页面开始第[%s]次重连';
    const STATUS_CODE_ERROR_MESSAGE = '[%s]页面连接错误状态码为:[%d]';

    protected $alreadyAnalyzedPageUrl = [];
    protected $readyAnalyzedPageUrl = [];
    protected $errorAnalyzedPageUrl = [];

    protected $continueSuffix = ['.png','.jpeg','.jpg','.gif'];

    public static $reconnectionCount = 0;

    public function attach(EventManagerInterface $events)
    {
        $this->listeners[] = $events->getSharedManager()->attach('*', GrabEvent::ANALYSIS_SITE_PAGE, [$this, 'setOrigUri']);
        $this->listeners[] = $events->getSharedManager()->attach('*', GrabEvent::ANALYSIS_SITE_PAGE, [$this, 'runAnalysis']);
    }


    public function setOrigUri(EventInterface $event)
    {
        /* @var $event \KpGrab\Event\Grab */
        $request = $event->getRequest();
        $console = $event->getConsole();
        $grabOptions = $event->getGrabOptions();
        if ($request instanceof Request) {
            $url = $request->getParam('url');
            $uri = UriFactory::factory($url);
        } else {
            /**
             * @todo 通过浏览器访问暂时无法提供url参数，所以先从配置里取出testurl
             */
            $uri = UriFactory::factory($grabOptions->getTestUrl());
        }

        /**
         * 检查Url参数，是否是Url,是否是绝对地址
         */
        if (!$uri->isValid() || !$uri->isAbsolute()) {
            if ($console) {
                $console->writeLine(AnalysisSitePage::URI_ERROR_MESSAGE, $grabOptions->getConsoleErrorMessageColor());
            } else {
                throw new InvalidArgumentException(AnalysisSitePage::URI_ERROR_MESSAGE);
            }
            $event->stopPropagation(true);
            return;
        }

        $event->setOrigUri($uri);

        $this->readyAnalyzedPageUrl[] = $uri->toString();


    }


    public function runAnalysis(EventInterface $event)
    {

        /* @var $event \KpGrab\Event\Grab */
        $grabOptions = $event->getGrabOptions();
        $httpClient = $event->getHttpClient();

        if (count($this->readyAnalyzedPageUrl) < 1) {
            $event->setAnalyzedPageUrl($this->alreadyAnalyzedPageUrl);
            return;
        }

        $url = array_shift($this->readyAnalyzedPageUrl);

        $urlInfo = $event->analysisAbsoluteUrl($url);

        $event->setMessage(sprintf(AnalysisSitePage::START_ANALYSIS_MESSAGE, $url) , $event->getName());

        /**
         * 开始连接地址，连接超时，重新连接
         */
        try {
            $response = $httpClient->setUri($url)->send();
        } catch (RuntimeException $e) {
            $event->setMessage(sprintf(AnalysisSitePage::RECONNECTION_MESSAGE, $url , ++AnalysisSitePage::$reconnectionCount) , $event->getName() , $grabOptions->getConsoleErrorMessageColor());

            if(AnalysisSitePage::$reconnectionCount >= $grabOptions->getMaxReconnectionCount()){
                AnalysisSitePage::$reconnectionCount = 0;
                $this->errorAnalyzedPageUrl[] = $url;
            }else{
                array_unshift($this->readyAnalyzedPageUrl, $url);
            }

            $this->runAnalysis($event);
            return;
        }

        AnalysisSitePage::$reconnectionCount = 0;

        /**
         * 检测状态，非200，加入errorAnalyzedPageUrl,跳过该连接
         */
        if ($response->getStatusCode() !== Response::STATUS_CODE_200) {
            $event->setMessage(sprintf(AnalysisSitePage::STATUS_CODE_ERROR_MESSAGE,$url,$response->getStatusCode()),$event->getName(),$grabOptions->getConsoleErrorMessageColor());
            $this->errorAnalyzedPageUrl[] = $url;
            $this->runAnalysis($event);
            return;
        }


        $this->alreadyAnalyzedPageUrl[] = $url;


        $document = new Document($response->getContent());
        $dom = new Document\Query();

        $findUrlList = [];

        foreach ($dom->execute('a', $document, Document\Query::TYPE_CSS) as $node) {
            $findUrlList[] =$node->getAttribute('href');
        }

        foreach ($dom->execute('form', $document, Document\Query::TYPE_CSS) as $node) {
            $findUrlList[] =$node->getAttribute('action');;
        }

        $uriValidator = new Uri(['allowRelative' => false]);

        foreach($findUrlList as $findUrl){

            if (!$uriValidator->isValid($findUrl)) {
                $findUrl = $urlInfo['scheme'] . '://' . $urlInfo['host']. $urlInfo['path'] .'/' . $findUrl;
            }

            $findUrl = rtrim($findUrl,'#');
            $findUrl = rtrim($findUrl,'/');

            // 检查需要忽略的后缀
            foreach($this->continueSuffix as $suffix){
                if(strrchr(strtolower($findUrl), $suffix) === $suffix){
                    continue 2;
                }
            }

            // 检查锚点跳过
            if(parse_url($findUrl,PHP_URL_FRAGMENT)){
                continue;
            }

            // 判断host是否是该网站，是否有添加过该页面
            if($event->getOrigUri()->getHost() === parse_url($findUrl,PHP_URL_HOST) &&
                !in_array($findUrl, $this->readyAnalyzedPageUrl) &&
                !in_array($findUrl, $this->alreadyAnalyzedPageUrl) &&
                !in_array($findUrl, $this->errorAnalyzedPageUrl)
            ){
                $this->readyAnalyzedPageUrl[] = $findUrl;
                $event->setMessage(sprintf(AnalysisSitePage::ADD_ANALYSIS_MESSAGE, $findUrl) , $event->getName());
            }
        }

        $this->runAnalysis($event);

    }

}