<?php
/**
 * Kittencup
 *
 * @date 2015 15/1/30 上午11:42
 * @copyright Copyright (c) 2014-2015 Kittencup. (http://www.kittencup.com)
 * @license   http://kittencup.com
 */
return [

    'router' => [
        'routes' => [
            'home' => [
                'type' => 'Literal',
                'options' => [
                    'route' => '/grabSite',
                    'defaults' => array(
                        '__NAMESPACE__' => 'KpGrab\Controller',
                        'controller' => 'Site',
                        'action' => 'index',
                    ),
                ]
            ]
        ]
    ],
    'console' => [
        'router' => [
            'routes' => [
                'grab_site' => [
                    'options' => [
                        'route' => 'grab site <url> [--saveDir=]',
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

    'controllers' => [
        'invokables' => [
            'KpGrab\Controller\Site' => 'KpGrab\Controller\Site'
        ]
    ],
    'view_manager' => [
        'template_path_stack' => [
            __DIR__ . '/../view',
        ],

    ],

    'kp_grab' => [
        'http_adapter' => 'Zend\Http\Client\Adapter\Curl',
        'http_adapter_options' => [
            'curloptions' => [
                CURLOPT_ENCODING => 'gzip',
                CURLOPT_FOLLOWLOCATION => false,
                CURLOPT_TIMEOUT => 10,
                CURLOPT_NOSIGNAL => 1
            ]
        ],
        'console_error_message_color' => \Zend\Console\ColorInterface::RED,
        'test_url' => 'http://demo.themepixels.com/webpage/amanda/index.html',
        'show_message' => true,
        'max_reconnection_count' => 5,
        'xdebug_max_nesting_level' => 600,
        'default_save_dir'=> realpath(__DIR__ . '/../data')
    ]

];