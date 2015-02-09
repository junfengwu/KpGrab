<?php
/**
 * Kittencup
 *
 * @date 2015 15/2/7 下午6:00
 * @copyright Copyright (c) 2014-2015 Kittencup. (http://www.kittencup.com)
 * @license   http://kittencup.com
 */

namespace KpGrab\Service\Factory;

use KpGrab\Options\Grab;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;


/**
 * Class GrabOptions
 * @package KpGrab\Service\Factory
 */
class GrabOptions implements FactoryInterface
{

    /**
     * @param ServiceLocatorInterface $serviceLocator
     * @return Grab
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {

        $config = $serviceLocator->get('config');

        return new Grab($config[Grab::CONFIG_KEY]);

    }


}