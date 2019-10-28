<?php
/**
 * Author: 沧澜
 * Date: 2019-10-28
 */

namespace CalJect\Productivity\Components\Comments;

use CalJect\Productivity\Contracts\Comments\ClassHeadComment;
use CalJect\Productivity\Contracts\DataProperty\TCallDataProperty;
use CalJect\Productivity\Models\FileInfo;
use CalJect\Productivity\Utils\CommentUtil;
use ReflectionClass;
use ReflectionProperty;

/**
 * Class CallDataPropertyHeadComment
 * @package CalJect\Productivity\Components\Comments
 * ---------- set ----------
 * @method $this setTagNote(string $tagNote)    默认检查属性说明注释部分tag[@note]
 * @method $this setTagVar(string $tagVar)      默认检查值类型注释部分tag[@var]
 * @method $this setDefVar(string $defVar)      默认值类型

 * ---------- get ----------
 * @method string getTagNote()    默认检查属性说明注释部分tag[@note]
 * @method string getTagVar()     默认检查值类型注释部分tag[@var]
 * @method string getDefVar()     默认值类型
 */
class CallDataPropertyHeadComment extends ClassHeadComment
{
    use TCallDataProperty;
    
    /**
     * @note 默认检查属性说明注释部分tag[@note]
     * @var string
     */
    protected $tagNote = 'note';
    
    /**
     * @note 默认检查值类型注释部分tag[@var]
     * @var string
     */
    protected $tagVar = 'var';
    
    /**
     * @note 默认值类型
     * @var string
     */
    protected $defVar = 'mixed';
    
    /**
     * @param FileInfo $fileInfo
     * @param ReflectionClass $refClass
     * @param string $filePath
     * @return string
     */
    protected function getComments(FileInfo $fileInfo, ReflectionClass $refClass, string $filePath): string
    {
        array_map(function (ReflectionProperty $property) use (&$setting, &$getting, &$noting, &$varMaxLen, &$setStrMaxLen, &$getStrMaxLen) {
            $getVar = CommentUtil::matchCommentTag($this->tagVar, $property->getDocComment(), $this->defVar);
            $ucName = ucfirst($name = $property->getName());
            $setVar = $getVar === 'mixed' ? '' : $getVar . ' ';
            $setting[$name] = $setStr = " * @method \$this set{$ucName}({$setVar}\${$name})";
            $getting[$name] = [
                'var' => $getVar,
                'str' => $getStr = " * @method \$var get{$ucName}()"
            ];
            $noting[$name] = CommentUtil::matchCommentTag('note', $property->getDocComment());
            $varMaxLen = ($varLen = strlen($getVar)) > $varMaxLen ? $varLen : $varMaxLen;
            $setStrMaxLen = ($strLen = strlen($setStr)) > $setStrMaxLen ? $strLen : $setStrMaxLen;
            $getStrMaxLen = ($strLen = strlen($getStr) - 4 + $varMaxLen) > $getStrMaxLen ? $strLen : $getStrMaxLen;
        }, $refClass->getProperties());
        if ($setStrMaxLen) {
            $comment = " * ---------- set ----------\n";
            foreach ($setting as $name => $str) {
                $comment .= ($noting[$name] ? str_pad($str, $setStrMaxLen + 4, ' ') . $noting[$name] : $str) . "\n";
            }
            $comment .= "\n * ---------- get ----------\n";
            foreach ($getting as $name => $content) {
                $str = str_replace('$var', str_pad($content['var'], $varMaxLen, ' '), $content['str']);
                $comment .= ($noting[$name] ? str_pad($str, $getStrMaxLen + 4, ' ') . $noting[$name] : $str) . "\n";
            }
        }
        return $this->commentWithClassHead($fileInfo, $comment ?? '');
    }
}