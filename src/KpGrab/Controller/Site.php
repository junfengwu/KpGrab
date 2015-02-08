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
use Zend\View\Model\ViewModel;

class Site extends AbstractActionController
{

    public function get_absolute_path($path) {
        $path = str_replace(array('/', '\\'), DIRECTORY_SEPARATOR, $path);
        $parts = array_filter(explode(DIRECTORY_SEPARATOR, $path), 'strlen');
        $absolutes = array();
        foreach ($parts as $part) {
            if ('.' == $part) continue;
            if ('..' == $part) {
                array_pop($absolutes);
            } else {
                $absolutes[] = $part;
            }
        }
        return implode(DIRECTORY_SEPARATOR, $absolutes);
    }

    public function indexAction()
    {
        //$alreadyAnalyzedUrl = $this->serviceLocator->get('GrabAnalysisSite')->init()->run();

        //var_dump($alreadyAnalyzedUrl);
        /* @var $grabEvent \KpGrab\Event\Grab */
        $grabEvent = $this->serviceLocator->get('GrabEvent');
        $this->events->trigger(Grab::ANALYSIS_SITE_PAGE, $grabEvent);
        $this->events->trigger(Grab::ANALYSIS_SITE_STATIC, $grabEvent);

        exit;


    }
}