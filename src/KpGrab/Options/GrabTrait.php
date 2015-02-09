<?php
/**
 * Kittencup
 *
 * @date 2015 15/2/9 上午11:54
 * @copyright Copyright (c) 2014-2015 Kittencup. (http://www.kittencup.com)
 * @license   http://kittencup.com
 */

namespace KpGrab\Options;

/**
 * Class GrabTrait
 * @package KpGrab\Options
 */
trait GrabTrait
{
    /**
     * @var Grab
     */
    protected $grabOptions;

    /**
     * @return Grab
     */
    public function getGrabOptions()
    {
        return $this->grabOptions;
    }

    /**
     * @param Grab $grabOptions
     * @return $this
     */
    public function setGrabOptions(Grab $grabOptions)
    {
        $this->grabOptions = $grabOptions;
        return $this;
    }
}