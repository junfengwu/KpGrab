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
use Zend\Cache\Storage\Adapter\Filesystem;
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

class SiteDownload implements ListenerAggregateInterface, ServiceLocatorAwareInterface
{
    use ServiceLocatorAwareTrait;
    use ListenerAggregateTrait;

    const RECONNECTION_MESSAGE = '[%s]需要下载的页面开始第[%s]次重连';

    public static $reconnectionCount = 0;

    protected $errorDownloadUrl = [];

    public function attach(EventManagerInterface $events)
    {
        $this->listeners[] = $events->getSharedManager()->attach('*', GrabEvent::SITE_DOWNLOAD, [$this, 'runSiteDownload']);
    }

    public function runSiteDownload(EventInterface $event)
    {

        /* @var $event \KpGrab\Event\Grab */

        $siteDownloadList = array_merge(
                $event->getAnalyzedPageUrl(),
                $event->getAnalyzedPageCss(),
                $event->getAnalyzedPageImage(),
                $event->getAnalyzedPageJs()
            );

        $httpClient = $event->getHttpClient();
        $grabOptions = $event->getGrabOptions();
        $saveDir = $event->getSaveDir();

        $rootName = md5($event->getOrigUri()->toString());

        while(count($siteDownloadList) > 0) {

            $url = array_shift($siteDownloadList);

            try {
                $response = $httpClient->setUri($url)->send();
            } catch (RuntimeException $e) {
                $event->setMessage(sprintf(SiteDownload::RECONNECTION_MESSAGE, $url , ++SiteDownload::$reconnectionCount) , $event->getName() , $grabOptions->getConsoleErrorMessageColor());

                if(SiteDownload::$reconnectionCount >= $grabOptions->getMaxReconnectionCount()){
                    SiteDownload::$reconnectionCount = 0;
                    $this->errorDownloadUrl[] = $url;
                }else{
                    array_unshift($siteDownloadList, $url);
                }

                continue;
            }

            SiteDownload::$reconnectionCount = 0;

            $downloadSaveDir = $saveDir . '/' . $rootName;

            if (!is_dir($downloadSaveDir)){
                mkdir($downloadSaveDir);
            }

            $urlInfo = parse_url($url);

            foreach (explode('/', trim(pathinfo($urlInfo['path'], PATHINFO_DIRNAME), '/')) as $folderName) {

                $downloadSaveDir .= '/' . $folderName;

                if (!is_dir($downloadSaveDir)) {
                    mkdir($downloadSaveDir);
                }

            }

            $pathInfo = pathinfo($urlInfo['path']);

            if (!array_key_exists('extension', $pathInfo)) {
                $fileName = '_default.html';
            } else {
                $fileName = $pathInfo['filename'] . '.' . $pathInfo['extension'];
            }

            file_put_contents($downloadSaveDir . '/' . $fileName, $response->getContent());
        }

    }

}