<?php
/**
 * Kittencup
 *
 * @date 2015 15/2/7 下午6:00
 * @copyright Copyright (c) 2014-2015 Kittencup. (http://www.kittencup.com)
 * @license   http://kittencup.com
 */

namespace KpGrab\Service\Factory;

use KpGrab\Event\Grab;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;


/**
 * Class GrabEvent
 * @package KpGrab\Service\Factory
 */
class GrabEvent implements FactoryInterface
{
    /**
     * @param ServiceLocatorInterface $serviceLocator
     * @return \KpGrab\Event\Grab
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $grabOptions = $serviceLocator->get('GrabOptions');
        $request = $serviceLocator->get('Request');
        $httpClient = $serviceLocator->get('GrabHttpClient');
        $grabResult = $serviceLocator->get('GrabResult');

        $grabEvent = new Grab();

        $grabEvent->setRequest($request)
            ->setGrabHttpClient($httpClient)
            ->setGrabResult($grabResult)
            ->setGrabOptions($grabOptions);

        return $grabEvent;

    }


}