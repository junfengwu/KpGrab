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
                    'route'    => '/grabSite',
                    'defaults' => array(
                        '__NAMESPACE__' => 'KpGrab\Controller',
                        'controller'    => 'Site',
                        'action'        => 'getList',
                    ),
                ]
            ]
        ]
    ],
    'console' =>[
        'router'=>[
            'routes' => [
                'grab_site' => [
                    'options' => [
                        'route'    => 'grab site <url>',
                        'defaults' => [
                            '__NAMESPACE__' => 'KpGrab\Controller',
                            'controller'    => 'Site',
                            'action'        => 'index',
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

    'kp_grab'=>[

    ]

];