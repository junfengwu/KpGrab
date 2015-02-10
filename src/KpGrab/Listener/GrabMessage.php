<?php
/**
 * Kittencup
 *
 * @date 2015 15/2/10 上午9:58
 * @copyright Copyright (c) 2014-2015 Kittencup. (http://www.kittencup.com)
 * @license   http://kittencup.com
 */


namespace KpGrab\Listener;

use KpGrab\Event\Grab as GrabEvent;
use KpGrab\Result\MessageInterface;
use Zend\Dom\Document;
use Zend\Http\Response;
use Zend\EventManager\EventManagerInterface;
use Zend\EventManager\ListenerAggregateInterface;
use Zend\EventManager\ListenerAggregateTrait;


/**
 * Class GrabAnalysisPage
 * @package KpGrab\Listener
 */
class GrabMessage implements ListenerAggregateInterface
{
    use ListenerAggregateTrait;

    /**
     * @param EventManagerInterface $events
     */
    public function attach(EventManagerInterface $events)
    {

        $event = [
            GrabEvent::GRAB_ANALYSIS_PAGE_PRE,
            GrabEvent::GRAB_ANALYSIS_PAGE_POST,
            GrabEvent::GRAB_ANALYSIS_PAGE_SUCCESS,
            GrabEvent::GRAB_ANALYSIS_STATIC_PRE,
            GrabEvent::GRAB_ANALYSIS_STATIC_POST,
            GrabEvent::GRAB_ANALYSIS_STATIC_SUCCESS,
            GrabEvent::GRAB_ANALYSIS_CSS_PRE,
            GrabEvent::GRAB_ANALYSIS_CSS_POST,
            GrabEvent::GRAB_ANALYSIS_CSS_SUCCESS,
            GrabEvent::GRAB_DOWNLOAD_PRE,
            GrabEvent::GRAB_DOWNLOAD_POST,
            GrabEvent::GRAB_DOWNLOAD_SUCCESS
        ];
        $this->listeners[] = $events->getSharedManager()->attach('*', $event, [$this, 'showMessage']);
    }

    public function showMessage(GrabEvent $event)
    {
        $grabResult = $event->getGrabResult();

        switch ($event->getName()) {
            // page
            case GrabEvent::GRAB_ANALYSIS_PAGE_PRE:
                $message = sprintf(MessageInterface::GRAB_ANALYSIS_PAGE_PRE_MESSAGE, $event->getParam('url'));
                break;
            case GrabEvent::GRAB_ANALYSIS_PAGE_POST:
                $message = MessageInterface::GRAB_ANALYSIS_PAGE_POST_MESSAGE;
                break;
            case GrabEvent::GRAB_ANALYSIS_PAGE_SUCCESS:
                $message = sprintf(MessageInterface::GRAB_ANALYSIS_PAGE_SUCCESS_MESSAGE, count($event->getGrabResult()->getGrabPageUrl()));
                break;
            // static
            case GrabEvent::GRAB_ANALYSIS_STATIC_PRE:
                $message = sprintf(MessageInterface::GRAB_ANALYSIS_STATIC_PRE_MESSAGE, $event->getParam('url'));
                break;
            case GrabEvent::GRAB_ANALYSIS_STATIC_POST:
                $message = MessageInterface::GRAB_ANALYSIS_STATIC_POST_MESSAGE;
                break;
            case GrabEvent::GRAB_ANALYSIS_STATIC_SUCCESS:
                $message = sprintf(MessageInterface::GRAB_ANALYSIS_STATIC_SUCCESS_MESSAGE, count($event->getGrabResult()->getGrabStaticUrl()));
                break;
            // css
            case GrabEvent::GRAB_ANALYSIS_CSS_PRE:
                $message = sprintf(MessageInterface::GRAB_ANALYSIS_CSS_PRE_MESSAGE, $event->getParam('url'));
                break;
            case GrabEvent::GRAB_ANALYSIS_CSS_POST:
                $message = MessageInterface::GRAB_ANALYSIS_CSS_POST_MESSAGE;
                break;
            case GrabEvent::GRAB_ANALYSIS_CSS_SUCCESS:
                $message = sprintf(MessageInterface::GRAB_ANALYSIS_CSS_SUCCESS_MESSAGE, count($event->getGrabResult()->getGrabStaticUrl()));
                break;
            // download
            case GrabEvent::GRAB_DOWNLOAD_PRE:
                $message = sprintf(MessageInterface::GRAB_DOWNLOAD_PRE_MESSAGE, $event->getParam('url'));
                break;
            case GrabEvent::GRAB_DOWNLOAD_POST:
                $message = MessageInterface::GRAB_DOWNLOAD_POST_MESSAGE;
                break;
            case GrabEvent::GRAB_DOWNLOAD_SUCCESS:
                $message = sprintf(MessageInterface::GRAB_DOWNLOAD_SUCCESS_MESSAGE);
                break;
            default:
                $message = false;
        }

        $message && $grabResult->setMessage($message, $event->getName());
    }

}