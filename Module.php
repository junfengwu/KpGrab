<?php
/**
 * Kittencup
 *
 * @date 2015 15/2/7 下午3:06
 * @copyright Copyright (c) 2014-2015 Kittencup. (http://www.kittencup.com)
 * @license   http://kittencup.com
 */

namespace KpGrab;

use Zend\EventManager\EventInterface;
use Zend\ModuleManager\Feature\AutoloaderProviderInterface;
use Zend\ModuleManager\Feature\BootstrapListenerInterface;
use Zend\ModuleManager\Feature\ConfigProviderInterface;
use Zend\ModuleManager\Feature\ConsoleBannerProviderInterface;
use Zend\ModuleManager\Feature\ConsoleUsageProviderInterface;
use Zend\ModuleManager\Feature\ControllerProviderInterface;
use Zend\ModuleManager\Feature\ServiceProviderInterface;
use Zend\Console\Adapter\AdapterInterface;

/**
 * Class Module
 * @package KpGrab
 */
class Module implements ConfigProviderInterface,
    AutoloaderProviderInterface,
    ServiceProviderInterface,
    BootstrapListenerInterface,
    ConsoleBannerProviderInterface,
    ConsoleUsageProviderInterface,
    ControllerProviderInterface
{
    /**
     * @return mixed
     */
    public function getConfig()
    {
        return include __DIR__ . '/config/module.config.php';
    }

    /**
     * @return array
     */
    public function getAutoloaderConfig()
    {
        return [
            'Zend\Loader\StandardAutoloader' => [
                'namespaces' => [
                    __NAMESPACE__ => __DIR__ . '/src/' . __NAMESPACE__,
                ],
            ],
        ];
    }

    /**
     * @return array
     */
    public function getControllerConfig()
    {
        return [
            'invokables' => [
                'KpGrab\Controller\Site' => 'KpGrab\Controller\Site'
            ]
        ];
    }

    /**
     * @return array
     */
    public function getServiceConfig()
    {
        return [
            'invokables' => [
                'XdebugListener' => 'KpGrab\Listener\Xdebug',
                'Grab' => 'KpGrab\Service\Invokable\Grab',
                'GrabPreListener' => 'KpGrab\Listener\GrabPre',
                'GrabAnalysisPageListener' => 'KpGrab\Listener\GrabAnalysisPage',
                'GrabAnalysisStaticListener' => 'KpGrab\Listener\GrabAnalysisStatic',
                'GrabAnalysisCssListener' => 'KpGrab\Listener\GrabAnalysisCss',
                'GrabDownloadListener' => 'KpGrab\Listener\GrabDownload',
                'GrabPostListener' => 'KpGrab\Listener\GrabPost'
            ],
            'factories' => [
                'GrabOptions' => 'KpGrab\Service\Factory\GrabOptions',
                'GrabHttpClient' => 'KpGrab\Service\Factory\GrabHttpClient',
                'GrabEvent' => 'KpGrab\Service\Factory\GrabEvent',
                'GrabResult' => 'KpGrab\Service\Factory\GrabResult'
            ]
        ];
    }

    /**
     * @param AdapterInterface $console
     * @return string
     */
    public function getConsoleBanner(AdapterInterface $console)
    {
        return "KpGrab Module 0.0.1,http://www.kittencup.com";
    }

    /**
     * @param AdapterInterface $console
     * @return array
     */
    public function getConsoleUsage(AdapterInterface $console)
    {
        return [
            'grab site <url> [--save-dir=] [--save-name=]' => '根据url抓取网站所有静态页面和静态文件',
            ['<url>', '要抓取的网站地址,比如http://www.kittencup.com/index.html'],
            ['--save-dir=DIR', '抓取的内容保存的目录,不填写默认根据配置提供,目录要可写'],
            ['--save-name=NAME', '抓取的内容保存的文件夹名，不填写随机生成']
        ];
    }

    /**
     * @param EventInterface $e
     * @return array
     */
    public function onBootstrap(EventInterface $e)
    {

        $application = $e->getApplication();
        $serviceManager = $application->getServiceManager();
        $eventManager = $application->getEventManager();


        if (extension_loaded('xdebug')) {
            $eventManager->attach($serviceManager->get('XdebugListener'));
        }

        $grabOptions = $serviceManager->get('GrabOptions');

        $eventManager->attach($serviceManager->get('GrabPreListener'));
        $eventManager->attach($serviceManager->get('GrabAnalysisPageListener'));
        $eventManager->attach($serviceManager->get('GrabAnalysisStaticListener'));
        $eventManager->attach($serviceManager->get('GrabAnalysisCssListener'));
        $eventManager->attach($serviceManager->get('GrabDownloadListener'));

        if ($grabOptions->getOutputError()) {
            $eventManager->attach($serviceManager->get('GrabPostListener'));
        }

    }

}
