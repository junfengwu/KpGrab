1.概述
--------
KpGrab是一个基于Zend Framweork 2模块，主要功能是抓取整站静态页面

2.安装
--------
[github下载](https://github.com/h112367/KpGrab.git) 或者 `composer require "h112367/kp-grab": "dev-master"`

```
#application.config.php
return [
	'modules' => [
        // ...
        'KpGrab',
    ],
];
```


3.使用
--------

```
php public/index.php grab site <url> [--save-dir=] [--save-name=]
```
* <url> 要抓取的网站地址,比如http://www.kittencup.com/index.html.
* --save-dir=DIR, 抓取的内容保存的目录,不填写默认根据配置提供,目录要可写.
* --save-name=NAME, 抓取的内容保存的文件夹名，不填写随机生成.

例子

```
php public/index.php grab site http://admindesigns.com/framework/dashboard.html  --save-dir=/Users/Kittencup/WebServer/zf2/data --save-name=admindesigns
```

4.配置
--------
具体的配置内容在KpGrab/config/module.config.php内，使用kp_grab键值

* http\_adapter => (String) 使用http连接方式
* http\_adapter\_options => (Array) http连接方式的选项
* console\_error\_message\_color => (Int) 控制台报错信息的颜色,使用Zend\Console\ColorInterface内常量
* show\_message => (bool) 显示抓取具体信息 
* max\_reconnection\_count => (int) 连接失败重新连接次数
* xdebug\_max\_nesting\_level => (int) xdebug下函数递归太多层可能会报错，尽量提高该配置,
* default\_save_dir => (String) 默认的保存文件夹
* grab\_allow\_page\_suffix => (Array) 允许抓取的页面后缀
* grab\_allow\_static\_suffix => (Array) 允许抓取的静态文件后缀
* output\_error => (bool) 是否将错误信息输出到文件中
* output\_error\_filename => (string) 保存错误信息的文件名

例子

```
'kp_grab' => [
        'http_adapter' => 'Zend\Http\Client\Adapter\Curl',
        'http_adapter_options' => [
            'curloptions' => [
                CURLOPT_ENCODING => 'gzip',
                CURLOPT_FOLLOWLOCATION => false,
                CURLOPT_TIMEOUT => 20,
                CURLOPT_NOSIGNAL => 1
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
```
