<?php
/**
 * Kittencup
 *
 * @date 2015 15/1/30 上午11:42
 * @copyright Copyright (c) 2014-2015 Kittencup. (http://www.kittencup.com)
 * @license   http://kittencup.com
 */

return [
    'console' => [
        'router' => [
            'routes' => [
                'grab_site' => [
                    'options' => [
                        'route' => 'grab site <url> [--save-dir=] [--save-name=]',
                        'defaults' => [
                            '__NAMESPACE__' => 'KpGrab\Controller',
                            'controller' => 'Site',
                            'action' => 'index'
                        ],
                    ]
                ]
            ]
        ]
    ],
    'kp_grab' => [
        'http_adapter' => 'Zend\Http\Client\Adapter\Curl',
        'http_adapter_options' => [
            'curloptions' => [
                CURLOPT_ENCODING => 'gzip',
                CURLOPT_FOLLOWLOCATION => false,
                //CURLOPT_COOKIE=>"Cookie:safedog-flow-item=;",
                CURLOPT_TIMEOUT => 20,
                CURLOPT_NOSIGNAL => 1,
                CURLOPT_USERAGENT => 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_10_2) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/40.0.2214.91 Safari/537.36'
            ]
        ],
        'console_error_message_color' => \Zend\Console\ColorInterface::RED,
        'show_message' => true,
        'max_reconnection_count' => 5,
        'xdebug_max_nesting_level' => 600,
        'default_save_dir' => realpath(__DIR__ . '/../data'),
        'grab_allow_page_suffix' => ['html'],
        'grab_allow_static_suffix' => ['png', 'jpeg', 'jpg', 'gif', 'css', 'js', 'woff', 'ttf', 'eot', 'svg'],
        'output_error' => true,
        'output_error_filename' => 'error.md'
    ]

];