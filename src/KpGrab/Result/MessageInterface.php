<?php
/**
 * Kittencup
 *
 * @date 2015 15/2/9 上午11:28
 * @copyright Copyright (c) 2014-2015 Kittencup. (http://www.kittencup.com)
 * @license   http://kittencup.com
 */

namespace KpGrab\Result;

use KpGrab\Exception\ExceptionInterface;
use Zend\Console\ColorInterface;
use Zend\Console\Adapter\AdapterInterface;

/**
 * Interface MessageInterface
 * @package KpGrab\Result
 */
interface MessageInterface
{
    const DEFAULT_CONSOLE_COLOR = ColorInterface::WHITE;
    const ERROR_MESSAGE = 'error';
    const NORMAL_MESSAGE = 'normal';

    const ERROR_SAVE_DIR_MESSAGE = '保存路径[%s]不存在或不可写';
    const ERROR_URL_MESSAGE = '请输入一个正确的url参数';
    const ERROR_RECONNECT_MESSAGE = '[%s]页面连接失败，准备进行[%s]次重连';
    const ERROR_CONNECT_FAIL_MESSAGE = '[%s]页面连接失败超过[%d]次，跳过该连接';
    const ERROR_CONNECT_CODE_MESSAGE = '[%s]页面连接错误状态码为:[%d]';
    const ERROR_UNKNOWN_MESSAGE = '[%s]未知错误:[%s]';

    /**
     * @param AdapterInterface $console
     * @return $this
     */
    public function setConsole(AdapterInterface $console);

    /**
     * @return AdapterInterface
     */
    public function getConsole();

    /**
     * @param $message ExceptionInterface|string
     * @param string $eventName
     * @param bool $isExit
     * @return $this
     */
    public function setMessage($message, $eventName, $isExit = false);

    /**
     * @return array
     */
    public function getMessages();

}