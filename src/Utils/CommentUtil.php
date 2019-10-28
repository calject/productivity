<?php
/**
 * Author: 沧澜
 * Date: 2019-10-28
 */

namespace CalJect\Productivity\Utils;

class CommentUtil
{
    
    /**
     * 匹配tag注释内容(单行匹配)
     * @param string $tag
     * @param string|false $docComment
     * @param mixed $default
     * @return bool|string|mixed
     */
    public static function matchCommentTag(string $tag, $docComment, $default = false)
    {
        return ($docComment && preg_match('/@' . $tag . '(.*)\\n/', $docComment, $arr) && isset($arr[1]) && $text = trim($arr[1]))
            ? $text : $default;
    }
    
}