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
    const ERROR_CONNECT_CODE_MESSAGE = '[%s]页面连接错误状态码为:[%d],该页面跳过!';
    const ERROR_UNKNOWN_MESSAGE = '[%s]未知错误:[%s]';

    const GRAB_ANALYSIS_PAGE_PRE_MESSAGE = '开始分析页面=>[%s]';
    const GRAB_ANALYSIS_PAGE_POST_MESSAGE = '=>页面分析结束';
    const GRAB_ANALYSIS_PAGE_SUCCESS_MESSAGE = '============整站页面分析完毕，页面总数为%d============';

    const GRAB_ANALYSIS_STATIC_PRE_MESSAGE = '开始分析页面的静态文件=>[%s]';
    const GRAB_ANALYSIS_STATIC_POST_MESSAGE = '=>页面的静态文件分析结束';
    const GRAB_ANALYSIS_STATIC_SUCCESS_MESSAGE = '============整站页面静态文件分析完毕，总获取%d个静态文件============';

    const GRAB_ANALYSIS_CSS_PRE_MESSAGE = '开始分析CSS文件=>[%s]';
    const GRAB_ANALYSIS_CSS_POST_MESSAGE = '=>CSS文件分析结束';
    const GRAB_ANALYSIS_CSS_SUCCESS_MESSAGE = '============整站CSS文件分析完毕，总获取%d个静态文件============';

    const GRAB_DOWNLOAD_PRE_MESSAGE = '开始下载文件=>[%s]';
    const GRAB_DOWNLOAD_POST_MESSAGE = '=>文件下载结束';
    const GRAB_DOWNLOAD_SUCCESS_MESSAGE = '============整站文件下载完毕============';
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