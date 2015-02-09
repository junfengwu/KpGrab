<?php
/**
 * Kittencup
 *
 * @date 2015 15/2/9 上午11:48
 * @copyright Copyright (c) 2014-2015 Kittencup. (http://www.kittencup.com)
 * @license   http://kittencup.com
 */

namespace KpGrab\Tools;

/**
 * Class Uri
 * @package KpGrab\Tools
 */
class Uri
{

    /**
     * @var \Zend\Validator\Uri
     */
    protected static $uriValidator;

    /**
     * @param $url
     * @return array
     */
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

    /**
     * @param $url
     * @return string
     */
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


    /**
     * @param $url
     * @return bool
     */
    public static function isAbsoluteUrl($url)
    {
        if (!Static::$uriValidator) {
            Static::$uriValidator = new \Zend\Validator\Uri(['allowRelative' => false]);
        }
        return Static::$uriValidator->isValid($url);
    }


    /**
     * @param $text
     * @return array
     */
    public static function getCssUrl($text)
    {
        $urls = [];

        $url_pattern = '(([^\\\\\'", \(\)]*(\\\\.)?)+)';
        $urlfunc_pattern = 'url\(\s*[\'"]?' . $url_pattern . '[\'"]?\s*\)';
        $pattern = '/(' .
            '(@import\s*[\'"]' . $url_pattern . '[\'"])' .
            '|(@import\s*' . $urlfunc_pattern . ')' .
            '|(' . $urlfunc_pattern . ')' . ')/iu';
        if (!preg_match_all($pattern, $text, $matches))
            return $urls;


        $urls['import'] = [];
        $urls['image'] = [];
        // @import '...'
        // @import "..."
        foreach ($matches[3] as $match)
            if (!empty($match))
                $urls['import'][] =
                    preg_replace('/\\\\(.)/u', '\\1', $match);

        // @import url(...)
        // @import url('...')
        // @import url("...")
        foreach ($matches[7] as $match)
            if (!empty($match))
                $urls['import'][] =
                    preg_replace('/\\\\(.)/u', '\\1', $match);

        // url(...)
        // url('...')
        // url("...")
        foreach ($matches[11] as $match)
            if (!empty($match))
                $urls['image'][] =
                    preg_replace('/\\\\(.)/u', '\\1', $match);

        return array_merge($urls['image'], $urls['import']);
    }
}