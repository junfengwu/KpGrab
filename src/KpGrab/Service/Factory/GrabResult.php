<?php
/**
 * Kittencup
 *
 * @date 2015 15/2/7 下午6:00
 * @copyright Copyright (c) 2014-2015 Kittencup. (http://www.kittencup.com)
 * @license   http://kittencup.com
 */

namespace KpGrab\Service\Factory;

use KpGrab\Result\Grab;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;


/**
 * Class GrabResult
 * @package KpGrab\Service\Factory
 */
class GrabResult implements FactoryInterface
{

    /**
     * @param ServiceLocatorInterface $serviceLocator
     * @return Grab
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {

        $grabResult = new Grab();

        $console = $serviceLocator->get('console');
        $grabOptions = $serviceLocator->get('GrabOptions');

        $grabResult->setConsole($console)->setGrabOptions($grabOptions);

        return $grabResult;

    }


}