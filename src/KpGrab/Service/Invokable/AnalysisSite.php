<?php
/**
 * Kittencup
 *
 * @date 2015 15/2/7 下午6:44
 * @copyright Copyright (c) 2014-2015 Kittencup. (http://www.kittencup.com)
 * @license   http://kittencup.com
 */

namespace KpGrab\Service\Invokable;

use Zend\Console\Request;
use Zend\Dom\Document;
use Zend\EventManager\EventManagerAwareInterface;
use Zend\EventManager\EventManagerAwareTrait;
use Zend\Http\Response;
use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\ServiceManager\ServiceLocatorAwareTrait;
use Zend\Uri\UriFactory;
use Zend\Validator\Uri;
use Zend\Http\Client\Adapter\Exception\RuntimeException;

/**
 * Class AnalysisSite
 * @package KpGrab\Service\Invokable
 */
class AnalysisSite implements ServiceLocatorAwareInterface, EventManagerAwareInterface
{

    use ServiceLocatorAwareTrait;
    use EventManagerAwareTrait;

    /**
     *
     */
    const HTML_SUFFIX = '.html';

    /**
     * @var
     */
    protected $request;

    /**
     * @var \Zend\Console\Adapter\AdapterInterface;
     */
    protected $console;

    /**
     * @var
     */
    protected $siteUrl;

    /**
     * @var \Zend\Http\Client;
     */
    protected $httpClient;

    /**
     * @var \Zend\Uri\Http
     */
    protected $siteUri;

    /**
     * @var Array
     */
    protected $alreadyAnalyzedUrl = [];

    /**
     * @var Array
     */
    protected $readyAnalyzedUrl = [];

    /**
     * @var Array
     */
    protected $errorAnalyzedUrl = [];

    /**
     * @var \KpGrab\Options\KpGrab
     */
    protected $grabOptions;

    /**
     * @return $this
     */
    public function init()
    {
        $this->request = $this->serviceLocator->get('Request');

        $this->httpClient = $this->serviceLocator->get('GrabHttpClient');
        $this->console = $this->serviceLocator->get('Console');
        $this->grabOptions = $this->serviceLocator->get('GrabOptions');


        /**
         * @todo siteUrl 必须是有html后缀的网站入口url
         */
        if ($this->request instanceof Request) {
            $this->siteUrl = $this->request->getParam('url');
        } else {
            // @todo 浏览器访问的话 要设置下route 这里先这样写
        }

        /**
         * 没有siteUrl，可能浏览器无参数访问，就当测试状态
         */
        if ($this->siteUrl === null) {
            $this->siteUri = $this->grabOptions->getTestUrl();
        }

        /**
         * @todo 是不是有更好的代码
         */
        $this->readyAnalyzedUrl[] = $this->siteUrl;
        $siteUrlInfo = explode('/', rtrim($this->siteUrl, '/'));
        array_pop($siteUrlInfo);
        $this->siteUrl = implode('/', $siteUrlInfo);
        $this->siteUri = UriFactory::factory($this->siteUrl);

        return $this;
    }

    /**
     * @return $this
     */
    public function run()
    {
        if (count($this->readyAnalyzedUrl) < 1) {
            return $this;
        }

        $url = array_shift($this->readyAnalyzedUrl);

        $this->console->writeLine(sprintf('开始分析%s页面', $url));

        /**
         * 开始连接地址，连接超时，重新连接
         */
        try {
            $response = $this->httpClient->setUri($url)->send();
        } catch (RuntimeException $e) {
            array_unshift($this->readyAnalyzedUrl, $url);
            $this->console->writeLine(sprintf('%s页面开始重连', $url), $this->grabOptions->getConsoleErrorMessageColor());
            $this->run();
            return;
        }

        /**
         * 检测状态，非200，加入errorAnalyzedUrl,跳过该连接
         */
        if ($response->getStatusCode() !== Response::STATUS_CODE_200) {
            $this->console->writeLine(sprintf('%s页面连接错误状态码为:%d', $url, $response->getStatusCode()), $this->grabOptions->getConsoleErrorMessageColor());
            $this->errorAnalyzedUrl[] = [
                'url' => $url,
                'statusCode' => $response->getStatusCode()
            ];
            $this->run();
        }

        $this->alreadyAnalyzedUrl[] = $url;

        $document = new Document($response->getContent());
        $dom = new Document\Query();

        foreach ($dom->execute('a', $document, Document\Query::TYPE_CSS) as $node) {
            $href = $node->getAttribute('href');
            $this->addUrl($href);
        }

        foreach ($dom->execute('form', $document, Document\Query::TYPE_CSS) as $node) {
            $action = $node->getAttribute('action');
            $this->addUrl($action);
        }

        $this->console->writeLine(sprintf('%s页面分析结束', $url));

        $this->run();

    }


    /**
     * @param $url
     * @return $this
     */
    public function addUrl($url)
    {
        if (Static::checkSuffix($url)) {

            $uriValidator = new Uri(['allowRelative' => false]);

            if (!$uriValidator->isValid($url)) {
                $url = $this->siteUrl . '/' . trim($url, '/');
            }

            if ($this->checkHost($url) &&
                !in_array($url, $this->readyAnalyzedUrl) &&
                !in_array($url, $this->alreadyAnalyzedUrl) &&
                !in_array($url, $this->errorAnalyzedUrl)
            ) {
                $this->readyAnalyzedUrl[] = $url;
                $this->console->writeLine(sprintf('新增准备分析页面%s', $url));
            }

        }
        return $this;
    }

    /**
     * @param $url
     * @return bool
     */
    public static function checkSuffix($url)
    {
        return strrchr(strtolower($url), AnalysisSite::HTML_SUFFIX) === AnalysisSite::HTML_SUFFIX;
    }

    /**
     * @param $url
     * @return bool
     */
    public function checkHost($url)
    {
        $urlInfo = parse_url($url);
        return $urlInfo['host'] === $this->siteUri->getHost();
    }

}