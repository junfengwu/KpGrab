<?php
/**
 * Kittencup
 *
 * @date 2015 15/2/8 上午10:37
 * @copyright Copyright (c) 2014-2015 Kittencup. (http://www.kittencup.com)
 * @license   http://kittencup.com
 */

namespace KpGrab\Listener;

use KpGrab\Event\Grab as GrabEvent;
use KpGrab\Exception\InvalidArgumentException;
use Zend\Console\Request;
use Zend\Dom\Document;
use Zend\EventManager\EventInterface;
use Zend\EventManager\EventManagerAwareInterface;
use Zend\EventManager\EventManagerAwareTrait;
use Zend\EventManager\EventManagerInterface;
use Zend\EventManager\ListenerAggregateInterface;
use Zend\EventManager\ListenerAggregateTrait;
use Zend\Http\Response;
use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\ServiceManager\ServiceLocatorAwareTrait;

use Zend\Uri\UriFactory;
use Zend\Http\Client\Adapter\Exception\RuntimeException;
use Zend\Validator\Uri;

class AnalysisSiteStatic implements ListenerAggregateInterface, ServiceLocatorAwareInterface, EventManagerAwareInterface
{
    use ServiceLocatorAwareTrait;
    use ListenerAggregateTrait;
    use EventManagerAwareTrait;

    protected $alreadyAnalyzedPageUrl = [];
    protected $readyAnalyzedPageUrl = [];
    protected $errorAnalyzedPageUrl = [];

    protected $continueSuffix = ['.png','.jpeg','.jpg','.gif'];

    public static $reconnectionCount = 0;

    public function attach(EventManagerInterface $events)
    {
        $this->listeners[] = $events->getSharedManager()->attach('*', GrabEvent::ANALYSIS_SITE_STATIC, [$this, 'runAnalysis']);
    }


    public function runAnalysis(EventInterface $event)
    {
        /* @var $event \KpGrab\Event\Grab */

        $console = $event->getConsole();

        foreach($event->getAnalyzedPageUrl() as $pageUrl){
            $console->writeLine($pageUrl);
        }
    }

}