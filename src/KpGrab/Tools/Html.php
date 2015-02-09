<?php
/**
 * Kittencup
 *
 * @date 2015 15/2/9 下午6:23
 * @copyright Copyright (c) 2014-2015 Kittencup. (http://www.kittencup.com)
 * @license   http://kittencup.com
 */

namespace KpGrab\Tools;

class Html
{   
    public static function format($html, $indentWith = '    ', $tagsWithoutIndentation = 'html,link,img,meta')
    {
        $html = str_replace(["\n", "\r", "\t"], ['', '', ' '], $html);
        $elements = preg_split('/(<.+>)/U', $html, -1, PREG_SPLIT_NO_EMPTY | PREG_SPLIT_DELIM_CAPTURE);
        $dom = self::parseDom($elements);
        $indent = 0;
        $output = array();
        foreach ($dom as $index => $element) {
            if ($element['opening']) {
                $output[] = "\n" . str_repeat($indentWith, $indent) . trim($element['content']);
                // make sure that only the elements who have not been blacklisted are being indented
                if (!in_array($element['type'], explode(',', $tagsWithoutIndentation))) {
                    ++$indent;
                }
            } else if ($element['standalone']) {
                $output[] = "\n" . str_repeat($indentWith, $indent) . trim($element['content']);
            } else if ($element['closing']) {
                --$indent;
                $lf = "\n" . str_repeat($indentWith, $indent);
                if (isset($dom[$index - 1]) && $dom[$index - 1]['opening']) {
                    $lf = '';
                }
                $output[] = $lf . trim($element['content']);
            } else if ($element['text']) {
                $output[] = "\n" . str_repeat($indentWith, $indent) . preg_replace('/ [ \t]*/', ' ', $element['content']);
            } else if ($element['comment']) {
                $output[] = "\n" . str_repeat($indentWith, $indent) . trim($element['content']);
            }
        }
        return trim(implode('', $output));
    }
}