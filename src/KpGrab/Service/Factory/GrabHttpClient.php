<?php
/**
 * Kittencup
 *
 * @date 2015 15/2/7 下午6:00
 * @copyright Copyright (c) 2014-2015 Kittencup. (http://www.kittencup.com)
 * @license   http://kittencup.com
 */

namespace KpGrab\Service\Factory;

use Zend\Http\Client;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;


class GrabHttpClient implements FactoryInterface
{

    public function createService(ServiceLocatorInterface $serviceLocator)
    {

        $themeForestOptions = $serviceLocator->get('GrabOptions');

        $adapterClass = $themeForestOptions->getHttpAdapter();
        $adapter = new $adapterClass;

        $adapter->setOptions($themeForestOptions->getHttpAdapterOptions());

        $client = new Client();
        $client->setAdapter($adapter);

        return $client;

    }


}