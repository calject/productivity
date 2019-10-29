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
 * @method $this setIsCallOther(bool $isCallOther)

 * ---------- get ----------
 * @method string  getTagNote()        默认检查属性说明注释部分tag
 * @method string  getTagVar()         默认检查值类型注释部分tag
 * @method string  getDefVar()         默认值类型
 * @method Closure getClassCheck()     类检查（检查是否生成注释）
 * @method int     getOptions()        配置参数
 * @method bool    getIsCallOther()

 * ---------- adapter ----------
 * @method $this|mixed tagNote(string $tagNote = null)           默认检查属性说明注释部分tag
 * @method $this|mixed tagVar(string $tagVar = null)             默认检查值类型注释部分tag
 * @method $this|mixed defVar(string $defVar = null)             默认值类型
 * @method $this|mixed classCheck(Closure $classCheck = null)    类检查（检查是否生成注释）
 * @method $this|mixed options(int $options = null)              配置参数
 * @method $this|mixed isCallOther(bool $isCallOther = null)
 */
class CallDataPropertyHeadComment extends ClassHeadComment
{
    use TCallDataProperty, TCallDataPropertyByName;
    
    const COM_SET = 1;          // 生成设置方法注释(get{$property}())
    const COM_GET = 1 << 1;     // 生成获取方法注释(set{$property}(@var $propertyName))
    const COM_APT = 1 << 2;     // 生成自动方法注释({property}(@var $propertyName = null))
    
    const PRO_NO_PRIVATE = 1 << 3;      // 不生成私有属性方法
    const PRO_NO_PROTECTED = 1 << 4;    // 不生成保护属性方法
    const PRO_NO_PUBLIC = 1 << 5;       // 不生成公共属性方法
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
    protected $options = 7;
    
    /**
     * @param FileInfo $fileInfo
     * @param ReflectionClass $refClass
     * @param string $filePath
     * @return string
     * @throws ClosureRunException
     */
    protected function getComments(FileInfo $fileInfo, ReflectionClass $refClass, string $filePath): string
    {
        array_map(function (ReflectionProperty $property) use (&$setting, &$getting, &$adapter, &$noting, &$varMaxLen, &$setStrMaxLen, &$getStrMaxLen, &$aptStrMaxLen) {
            $getVar = CommentUtil::matchCommentTag($this->tagVar, $property->getDocComment(), $this->defVar);
            $ucName = ucfirst($name = $property->getName());
            $setVar = $getVar === 'mixed' ? '' : $getVar . ' ';
            $setting[$name] = $setStr = " * @method \$this set{$ucName}({$setVar}\${$name})";
            $getting[$name] = [
                'var' => $getVar,
                'str' => $getStr = " * @method \$var get{$ucName}()"
            ];
            $adapter[$name] = $aptStr = " * @method \$this|mixed {$name}({$setVar}\${$name} = null)";
            $noting[$name] = CommentUtil::matchCommentTag('note', $property->getDocComment());
            $varMaxLen = ($varLen = strlen($getVar)) > $varMaxLen ? $varLen : $varMaxLen;
            $setStrMaxLen = ($strLen = strlen($setStr)) > $setStrMaxLen ? $strLen : $setStrMaxLen;
            $getStrMaxLen = ($strLen = strlen($getStr) - 4 + $varMaxLen) > $getStrMaxLen ? $strLen : $getStrMaxLen;
            $aptStrMaxLen = ($strLen = strlen($aptStr)) > $aptStrMaxLen ? $strLen : $aptStrMaxLen;
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
            })->bind(self::COM_APT, function () use ($adapter, $noting, $aptStrMaxLen, &$comment) {
                $comment .= "\n * ---------- adapter ----------\n";
                foreach ($adapter as $name => $str) {
                    $comment .= ($noting[$name] ? str_pad($str, $aptStrMaxLen + 4, ' ') . $noting[$name] : $str) . "\n";
                }
            })->handle();
        }
        return $this->commentWithClassHead($fileInfo, $comment ?? '');
    }
}