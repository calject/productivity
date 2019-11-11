<?php
/**
 * Author: 沧澜
 * Date: 2019-11-11
 */

namespace CalJect\Productivity\Components\Annotations;

use Closure;

/**
 * Class AnnotationTag
 * @package CalJect\Productivity\Components\Annotations
 */
class AnnotationTag
{
    
    /**
     * 匹配@tag(content)内容部分并转换为数组
     * @param string $docComment
     * @param string $tag
     * @param bool $default
     * @example @tag(api='name') / @tag(api='name', name='name') ==> ['api' => 'name'] / ['api' => 'name', 'name' => 'name']
     * @return array|bool|mixed
     */
    public static function matchTagContentToArray(string $docComment, string $tag, $default = false)
    {
        if ($content = self::matchTagContent($docComment, $tag, $default)) {
            return self::matchKeyValues($content, $default);
        } else {
            return $default;
        }
    }
    
    /**
     * 匹配@tag(content)内容部分(content)
     * @param string $docComment
     * @param string $tag
     * @param mixed $default
     * @example @tag(name) / @tag(api='name', name='name') ==> 'name' / api='name', name='name'
     * @return string|mixed
     */
    public static function matchTagContent(string $docComment, string $tag, $default = false)
    {
        if (preg_match('/\*[ ]*@' . $tag . '\((.*)\)\\n/s', $docComment, $tagComment) && $tagComment[1]) {
            return str_replace("\n", '', str_replace('*', '', $tagComment[1]));
        } else {
            return $default;
        }
    }
    
    /**
     * 匹配@tag内容
     * @param string $docComment
     * @param string $tag
     * @param mixed $default
     * @example @tag('api') / @tag(api)
     * @return array|mixed
     */
    public static function matchTagTextContent(string $docComment, string $tag, $default = false)
    {
        if (preg_match('/\*[ ]*@' . $tag . "\('?([^'()]*)'?\)\n/", $docComment, $tagComment) && $tagComment[1]) {
            return $tagComment[1];
        } else {
            return $default;
        }
    }
    
    /**
     * 将字符串(key='values')转换为键值对
     * @param string $docComment
     * @param mixed $default
     * @example a='a', b='b' ==> ['a' => 'a', 'b' => 'b']
     * @return array|mixed
     */
    public static function matchKeyValues(string $docComment, $default = false)
    {
        if (preg_match_all("/(\w*)='([^'()]*)'/", $docComment, $values) && $values[1]) {
            return array_combine($values[1], $values[2]);
        } else {
            return $default;
        }
    }
    
    /**
     * 匹配@tag(content)为数组键值 => ['tag' => 'content']
     * @param string $docComment
     * @param mixed $default
     * @example @tag1(v1)\n @tag2(v2) ==> ['tag1' => 'v1', 'tag2' => 'v2']
     * @return array|mixed
     */
    public static function matchTagKeyValues(string $docComment, $default = false)
    {
        if (preg_match_all("/\*[ ]*@(\w*)\('?([^'()]*)'?\)\n/", $docComment, $tagComment) && $tagComment[0]) {
            return array_combine($tagComment[1], $tagComment[2]);
        } else {
            return $default;
        }
    }
    
    /**
     * 匹配@tag(content)内容部分(content)
     * @param string $docComment
     * @param mixed $default
     * @param Closure $handle
     * @example @tag(v1) ==> v1
     * @return bool|mixed
     */
    public static function matchValue(string $docComment, $default = false, Closure $handle = null)
    {
        if (preg_match("/\('?([^'()]*)'?\)/", $docComment, $values) && $values[1]) {
            return $handle ? call_user_func($handle, $values[1]) : $values[1];
        } else {
            return $default;
        }
    }
}