<?php
/**
 * Kittencup
 *
 * @date 2015 15/2/9 下午6:23
 * @copyright Copyright (c) 2014-2015 Kittencup. (http://www.kittencup.com)
 * @license   http://kittencup.com
 */

namespace KpGrab\Tools;

/**
 * Class Html
 * @package KpGrab\Tools
 */
class Html
{
    /**
     * @param $html
     * @param string $indentWith
     * @param string $tagsWithoutIndentation
     * @return string
     */
    public static function format($html, $indentWith = '    ', $tagsWithoutIndentation = 'html,link,img,meta')
    {
        $html = str_replace(["\n", "\r", "\t"], ['', '', ' '], $html);
        $elements = preg_split('/(<.+>)/U', $html, -1, PREG_SPLIT_NO_EMPTY | PREG_SPLIT_DELIM_CAPTURE);
        $dom = Static::parseDom($elements);
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

    /**
     * @param array $elements
     * @return array
     */
    public static function parseDom(Array $elements)
    {
        $dom = array();
        foreach ($elements as $element) {
            $isText = false;
            $isComment = false;
            $isClosing = false;
            $isOpening = false;
            $isStandalone = false;
            $currentElement = trim($element);
            // comment
            if (strpos($currentElement, '<!') === 0) {
                $isComment = true;
            } else if (strpos($currentElement, '</') === 0) {
                $isClosing = true;
            } else if (preg_match('/\/>$/', $currentElement)) {
                $isStandalone = true;
            } else if (strpos($currentElement, '<') === 0) {
                $isOpening = true;
            } else {
                $isText = true;
            }
            $dom[] = array(
                'text' => $isText,
                'comment' => $isComment,
                'closing' => $isClosing,
                'opening' => $isOpening,
                'standalone' => $isStandalone,
                'content' => $element,
                'type' => preg_replace('/^<\/?(\w+)[ >].*$/U', '$1', $element)
            );
        }
        return $dom;
    }
}