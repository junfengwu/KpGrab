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
use Zend\ModuleManager\Feature\ServiceProviderInterface;
use Zend\Mvc\MvcEvent;

class Module implements ConfigProviderInterface,
    AutoloaderProviderInterface, ServiceProviderInterface, BootstrapListenerInterface
{
    public function getConfig()
    {
        return include __DIR__ . '/config/module.config.php';
    }

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

    public function getServiceConfig()
    {
        return [
            'invokables' => [
                'GrabAnalysisSite' => 'KpGrab\Service\Invokable\AnalysisSite',
                'AnalysisSitePageListener' => 'KpGrab\Listener\AnalysisSitePage',
                'AnalysisSiteStaticListener' => 'KpGrab\Listener\AnalysisSiteStatic',
                'AnalysisSiteCss'=>'KpGrab\Listener\AnalysisSiteCss',
                'SiteDownloadListener' => 'KpGrab\Listener\SiteDownload',
                'XdebugListener' => 'KpGrab\Listener\Xdebug'
            ],
            'factories' => [
                'GrabOptions' => 'KpGrab\Service\Factory\GrabOptions',
                'GrabHttpClient' => 'KpGrab\Service\Factory\GrabHttpClient',
                'GrabEvent' => 'KpGrab\Service\Factory\GrabEvent',
            ]
        ];
    }


    public function getViewHelperConfig()
    {

        return [
            'invokables' => [

            ]
        ];
    }

    public function onBootstrap(EventInterface $e)
    {

        $application = $e->getApplication();
        $serviceManager = $application->getServiceManager();
        $eventManager = $application->getEventManager();


        if (extension_loaded('xdebug')) {
            $eventManager->attach($serviceManager->get('XdebugListener'));
        }

        $eventManager->attach($serviceManager->get('AnalysisSitePageListener'));
        $eventManager->attach($serviceManager->get('AnalysisSiteStaticListener'));
        $eventManager->attach($serviceManager->get('AnalysisSiteCss'));
        $eventManager->attach($serviceManager->get('SiteDownloadListener'));
    }

}
