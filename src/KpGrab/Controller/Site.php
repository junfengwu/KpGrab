<?php
/**
 * Kittencup
 *
 * @date 2015 15/2/7 下午7:52
 * @copyright Copyright (c) 2014-2015 Kittencup. (http://www.kittencup.com)
 * @license   http://kittencup.com
 */

namespace KpGrab\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

class Site extends AbstractActionController
{

    public function indexAction()
    {


        $alreadyAnalyzedUrl = $this->serviceLocator->get('KpGrabAnalysisSite')->init()->run();

        var_dump($alreadyAnalyzedUrl);

        exit;


    }
}