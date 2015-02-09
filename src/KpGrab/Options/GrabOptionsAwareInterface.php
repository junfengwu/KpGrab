<?php
/**
 * Kittencup
 *
 * @date 2015 15/2/9 上午11:58
 * @copyright Copyright (c) 2014-2015 Kittencup. (http://www.kittencup.com)
 * @license   http://kittencup.com
 */

namespace KpGrab\Options;

interface GrabOptionsAwareInterface
{
    public function getGrabOptions();

    public function setGrabOptions(Grab $garbOptions);
}
