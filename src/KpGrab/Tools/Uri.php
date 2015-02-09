<?php
/**
 * Kittencup
 *
 * @date 2015 15/2/9 上午11:48
 * @copyright Copyright (c) 2014-2015 Kittencup. (http://www.kittencup.com)
 * @license   http://kittencup.com
 */

namespace KpGrab\Tools;

class Uri
{

    protected static $uriValidator;

    public static function parseAbsoluteUrl($url)
    {

        $url = rtrim($url, '/');

        $urlInfo = parse_url($url);

        if (!isset($urlInfo['path'])) {
            $urlInfo['path'] = '/';
        } else {
            $pathInfo = pathinfo($urlInfo['path']);

            if (isset($pathInfo['extension'])) {
                $urlInfo['path'] = str_replace($pathInfo['filename'] . '.' . $pathInfo['extension'], '', $urlInfo['path']);
                $urlInfo['extension'] = strtolower($pathInfo['extension']);
                $urlInfo['filename'] = $pathInfo['filename'];
            }

            $urlInfo['path'] = rtrim($urlInfo['path'], '/');

        }


        return $urlInfo;

    }

    public static function getRealUrl($url)
    {
        $urlInfo = parse_url($url);

        if (isset($urlInfo['path'])) {
            $url = str_replace(array('/', '\\'), DIRECTORY_SEPARATOR, $urlInfo['path']);
            $parts = array_filter(explode(DIRECTORY_SEPARATOR, $url), 'strlen');
            $absolutes = array();
            foreach ($parts as $part) {
                if ('.' == $part) continue;
                if ('..' == $part) {
                    array_pop($absolutes);
                } else {
                    $absolutes[] = $part;
                }
            }

            $urlInfo['path'] = implode(DIRECTORY_SEPARATOR, $absolutes);
        } else {
            $urlInfo['path'] = '';
        }

        $query = '';
        if (array_key_exists('query', $urlInfo)) {
            $query = '?' . $urlInfo['query'];
        }

        return $urlInfo['scheme'] . '://' . $urlInfo['host'] . '/' . $urlInfo['path'] . $query;
    }


    public static function isAbsoluteUrl($url)
    {
        if(!Static::$uriValidator) {
            Static::$uriValidator = new \Zend\Validator\Uri(['allowRelative' => false]);
        }
        return Static::$uriValidator->isValid($url);
    }
}