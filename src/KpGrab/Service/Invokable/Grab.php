<?php
/**
 * Kittencup
 *
 * @date 2015 15/2/9 上午11:41
 * @copyright Copyright (c) 2014-2015 Kittencup. (http://www.kittencup.com)
 * @license   http://kittencup.com
 */

namespace KpGrab\Service\Invokable;

use Zend\EventManager\EventManagerAwareInterface;
use Zend\EventManager\EventManagerAwareTrait;
use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\ServiceManager\ServiceLocatorAwareTrait;
use KpGrab\Event\Grab as GrabEvent;

class Grab implements ServiceLocatorAwareInterface, EventManagerAwareInterface
{
    use ServiceLocatorAwareTrait;
    use EventManagerAwareTrait;

    protected $triggerEvents = [
        GrabEvent::GRAB_PRE,
        GrabEvent::GRAB_ANALYSIS_PAGE,
        GrabEvent::GRAB_ANALYSIS_STATIC,
        GrabEvent::GRAB_ANALYSIS_CSS,
        GrabEvent::GRAB_DOWNLOAD,
        GrabEvent::GRAB_POST
    ];

    public function run()
    {
        /* @var $grabEvent \KpGrab\Event\Grab */
          $grabEvent = $this->serviceLocator->get('GrabEvent');

          foreach($this->triggerEvents as $event){
              $this->events->trigger($event,$grabEvent);
          }

    }
}