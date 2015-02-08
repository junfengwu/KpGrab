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
use Zend\EventManager\EventManagerAwareInterface;
use Zend\EventManager\EventManagerAwareTrait;
use Zend\EventManager\EventManagerInterface;
use Zend\EventManager\ListenerAggregateInterface;
use Zend\EventManager\ListenerAggregateTrait;
use Zend\Http\Response;
use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\ServiceManager\ServiceLocatorAwareTrait;

use Zend\Uri\UriFactory;
use Zend\Http\Client\Adapter\Exception\RuntimeException;
use Zend\Validator\Uri;

class AnalysisSiteStatic implements ListenerAggregateInterface, ServiceLocatorAwareInterface, EventManagerAwareInterface
{
    use ServiceLocatorAwareTrait;
    use ListenerAggregateTrait;
    use EventManagerAwareTrait;

    const RECONNECTION_MESSAGE = '[%s]页面开始第[%s]次重连';
    const ADD_ANALYSIS_MESSAGE = '新增静态文件[%s]';

    protected $allowImageSuffix = ['jpg','jpeg','gif','png'];
    protected $allowJsSuffix = ['js'];
    protected $allowCssSuffix = ['css'];

    protected $analyzedPageCss = [];
    protected $analyzedPageJs = [];
    protected $analyzedPageImage = [];

    protected $errorAnalyzedPageUrl = [];

    public static $reconnectionCount = 0;

    public function attach(EventManagerInterface $events)
    {
        $this->listeners[] = $events->getSharedManager()->attach('*', GrabEvent::ANALYSIS_SITE_STATIC, [$this, 'runAnalysis']);
    }


    public function runAnalysis(EventInterface $event)
    {
        /* @var $event \KpGrab\Event\Grab */
        $grabOptions = $event->getGrabOptions();
        $console = $event->getConsole();
        $httpClient = $event->getHttpClient();

        $pageUrls = $event->getAnalyzedPageUrl();

        while(count($pageUrls) > 0){

            $url = array_shift($pageUrls);

            $urlInfo = $event->analysisAbsoluteUrl($url);

            try {
                $response = $httpClient->setUri($url)->send();
            } catch (RuntimeException $e) {
                $event->setMessage(sprintf(AnalysisSiteStatic::RECONNECTION_MESSAGE, $url , ++AnalysisSiteStatic::$reconnectionCount) , $event->getName() , $grabOptions->getConsoleErrorMessageColor());

                if(AnalysisSitePage::$reconnectionCount >= $grabOptions->getMaxReconnectionCount()){
                    AnalysisSitePage::$reconnectionCount = 0;
                    $this->errorAnalyzedPageUrl[] = $url;
                }else{
                    array_unshift($pageUrls, $url);
                }

                continue;
            }

            AnalysisSitePage::$reconnectionCount = 0;

            $document = new Document($response->getContent());
            $dom = new Document\Query();

            $findUrlList = [];

            foreach ($dom->execute('link', $document, Document\Query::TYPE_CSS) as $node) {
                $findUrlList[] =$node->getAttribute('href');
            }

            foreach ($dom->execute('script', $document, Document\Query::TYPE_CSS) as $node) {
                $src = $node->getAttribute('src');
                if($src){
                    $findUrlList[] = $src;
                }
            }

            foreach ($dom->execute('img', $document, Document\Query::TYPE_CSS) as $node) {
                $findUrlList[] =$node->getAttribute('src');
            }

            $uriValidator = new Uri(['allowRelative' => false]);

            foreach($findUrlList as $findUrl){

                if (!$uriValidator->isValid($findUrl)) {
                    $findUrl = $urlInfo['scheme'] . '://' . $urlInfo['host']. $urlInfo['path'] .'/' . $findUrl;
                }

                if($event->getOrigUri()->getHost() !== parse_url($findUrl,PHP_URL_HOST)){
                    continue;
                }

                // 解决 ../../../ 问题
                $findUrl = $event->getRealUrl($findUrl);

                $findUrlExtension = pathinfo($findUrl,PATHINFO_EXTENSION);

                if($findUrlExtension) {
                    if (in_array($findUrlExtension, $this->allowCssSuffix) && !in_array($findUrl, $this->analyzedPageCss)) {
                        $this->analyzedPageCss[] = $findUrl;
                    } else if (in_array($findUrlExtension, $this->allowJsSuffix) && !in_array($findUrl, $this->analyzedPageJs)) {
                        $this->analyzedPageJs[] = $findUrl;
                    } else if (in_array($findUrlExtension, $this->allowImageSuffix) && !in_array($findUrl, $this->analyzedPageImage)) {
                        $this->analyzedPageImage[] = $findUrl;
                    } else{
                        // 后缀不对或者已添加过
                        continue;
                    }

                    $event->setMessage(sprintf(AnalysisSiteStatic::ADD_ANALYSIS_MESSAGE, $findUrl) , $event->getName());

                }

            }

        }

        $event->setAnalyzedPageImage($this->analyzedPageImage);
        $event->setAnalyzedPageJs($this->analyzedPageJs);
        $event->setAnalyzedPageCss($this->analyzedPageCss);
    }

}