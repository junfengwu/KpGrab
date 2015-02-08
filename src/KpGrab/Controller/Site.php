<?php
/**
 * Kittencup
 *
 * @date 2015 15/2/7 下午7:52
 * @copyright Copyright (c) 2014-2015 Kittencup. (http://www.kittencup.com)
 * @license   http://kittencup.com
 */

namespace KpGrab\Controller;

use KpGrab\Event\Grab;
use Zend\Mvc\Controller\AbstractActionController;


class Site extends AbstractActionController
{

    public function indexAction()
    {

        /* @var $grabEvent \KpGrab\Event\Grab */
        $grabEvent = $this->serviceLocator->get('GrabEvent');
        $this->events->trigger(Grab::ANALYSIS_SITE_PAGE, $grabEvent);
        $this->events->trigger(Grab::ANALYSIS_SITE_STATIC, $grabEvent);
        $this->events->trigger(Grab::ANALYSIS_SITE_CSS, $grabEvent);
        $this->events->trigger(Grab::SITE_DOWNLOAD, $grabEvent);
        exit;


    }
}