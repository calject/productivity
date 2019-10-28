<?php
/**
 * Author: 沧澜
 * Date: 2019-10-28
 */

namespace CalJect\Productivity\Components\Comments;

use CalJect\Productivity\Components\Criteria\Criteria;
use CalJect\Productivity\Contracts\Comments\ClassHeadComment;
use CalJect\Productivity\Contracts\DataProperty\TCallDataProperty;
use CalJect\Productivity\Contracts\DataProperty\TCallDataPropertyByName;
use CalJect\Productivity\Exceptions\ClosureRunException;
use CalJect\Productivity\Models\FileInfo;
use CalJect\Productivity\Utils\CommentUtil;
use Closure;
use ReflectionClass;
use ReflectionProperty;

/**
 * Class CallDataPropertyHeadComment
 * @package CalJect\Productivity\Components\Comments
 * ---------- set ----------
 * @method $this setTagNote(string $tagNote)           默认检查属性说明注释部分tag
 * @method $this setTagVar(string $tagVar)             默认检查值类型注释部分tag
 * @method $this setDefVar(string $defVar)             默认值类型
 * @method $this setClassCheck(Closure $classCheck)    类检查（检查是否生成注释）
 * @method $this setOptions(int $options)              配置参数

 * ---------- get ----------
 * @method string  getTagNote()       默认检查属性说明注释部分tag
 * @method string  getTagVar()        默认检查值类型注释部分tag
 * @method string  getDefVar()        默认值类型
 * @method Closure getClassCheck()    类检查（检查是否生成注释）
 * @method int     getOptions()       配置参数
 */
class CallDataPropertyHeadComment extends ClassHeadComment
{
    use TCallDataProperty, TCallDataPropertyByName;
    
    const COM_SET = 1;          // 生成设置方法注释(get{$property}())
    const COM_GET = 1 << 1;     // 生成获取方法注释(set{$property}(@var $propertyName))
    const COM_APT = 1 << 2;     // 生成自动方法注释({property}(@var $propertyName = null))
    
    /**
     * @note 默认检查属性说明注释部分tag
     * @var string
     * @expain 示例 note => @note
     */
    protected $tagNote = 'note';
    
    /**
     * @note 默认检查值类型注释部分tag
     * @var string
     * @expain 示例 var => @var
     */
    protected $tagVar = 'var';
    
    /**
     * @note 默认值类型
     * @var string
     */
    protected $defVar = 'mixed';
    
    /**
     * @note 类检查（检查是否生成注释）
     * @var Closure
     * @expain Closure 闭包响应bool值
     */
    protected $classCheck;
    
    /**
     * @note 配置参数
     * @var int
     */
    protected $options = 3;
    
    /**
     * @param FileInfo $fileInfo
     * @param ReflectionClass $refClass
     * @param string $filePath
     * @return string
     * @throws ClosureRunException
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
        $options = Criteria::opts($this->options);
        if ($setStrMaxLen) {
            $options->bind(self::COM_SET, function () use ($setting, $noting, $setStrMaxLen, &$comment) {
                $comment .= " * ---------- set ----------\n";
                foreach ($setting as $name => $str) {
                    $comment .= ($noting[$name] ? str_pad($str, $setStrMaxLen + 4, ' ') . $noting[$name] : $str) . "\n";
                }
            })->bind(self::COM_GET, function () use ($getting, $noting, $varMaxLen, $getStrMaxLen, &$comment) {
                $comment .= "\n * ---------- get ----------\n";
                foreach ($getting as $name => $content) {
                    $str = str_replace('$var', str_pad($content['var'], $varMaxLen, ' '), $content['str']);
                    $comment .= ($noting[$name] ? str_pad($str, $getStrMaxLen + 4, ' ') . $noting[$name] : $str) . "\n";
                }
            })->handle();
        }
        return $this->commentWithClassHead($fileInfo, $comment ?? '');
    }
}