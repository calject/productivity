<?php
/**
 * Author: 沧澜
 * Date: 2019-10-28
 */

namespace CalJect\Productivity\Components\Comments;

use CalJect\Productivity\Components\Check\CkOpt;
use CalJect\Productivity\Components\Criteria\Criteria;
use CalJect\Productivity\Contracts\Comments\ClassHeadComment;
use CalJect\Productivity\Contracts\DataProperty\TCallDataProperty;
use CalJect\Productivity\Contracts\DataProperty\TCallDataPropertyByName;
use CalJect\Productivity\Exceptions\ClosureRunException;
use CalJect\Productivity\Models\FileInfo;
use CalJect\Productivity\Utils\CommentUtil;
use ReflectionClass;
use ReflectionProperty;

/**
 * Class CallDataPropertyHeadComment
 * @package CalJect\Productivity\Components\Comments
 * ---------- set ----------
 * @method $this setTagNote(string $tagNote)    默认检查属性说明注释部分tag
 * @method $this setTagVar(string $tagVar)      默认检查值类型注释部分tag
 * @method $this setDefVar(string $defVar)      默认值类型
 * @method $this setOptions(int $options)       默认配置参数
 * 
 * ---------- get ----------
 * @method string getTagNote()    默认检查属性说明注释部分tag
 * @method string getTagVar()     默认检查值类型注释部分tag
 * @method string getDefVar()     默认值类型
 * @method int    getOptions()    默认配置参数
 * 
 * ---------- apt ----------
 * @method $this|mixed tagNote(string $tagNote = null)    默认检查属性说明注释部分tag
 * @method $this|mixed tagVar(string $tagVar = null)      默认检查值类型注释部分tag
 * @method $this|mixed defVar(string $defVar = null)      默认值类型
 * @method $this|mixed options(int $options = null)       默认配置参数
 */
class CallDataPropertyHeadComment extends ClassHeadComment
{
    use TCallDataProperty, TCallDataPropertyByName;
    
    const COM_ALL = (1 << 4) - 1;           // all
    const COM_ALL_NO_PRO = (1 << 4) - 2;    // all && no property
    
    const COM_PRO = 1;                      // 生成属性说明注释
    const COM_SET = 1 << 1;                 // 生成设置方法注释(get{$property}())
    const COM_GET = 1 << 2;                 // 生成获取方法注释(set{$property}(@var $propertyName))
    const COM_APT = 1 << 3;                 // 生成自动方法注释({property}(@var $propertyName = null))
    
    const PRO_NO_PRIVATE = 1 << 4;          // 不生成私有属性方法
    const PRO_NO_PROTECTED = 1 << 5;        // 不生成保护属性方法
    const PRO_NO_PUBLIC = 1 << 6;           // 不生成公共属性方法
    
    const OPT_CREATE_CURRENT = 1 << 7;      // 只生成当前类定义的属性(不包括继承及use属性)
    
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
     * @note 默认配置参数
     * @expain 生成 GET SET APT / PRO_NO_PRIVATE
     * @var int
     */
    protected $options = self::COM_ALL - 1 | self::PRO_NO_PRIVATE;
    
    /**
     * @param FileInfo $fileInfo
     * @param ReflectionClass $refClass
     * @param string $filePath
     * @return string
     * @throws ClosureRunException
     */
    protected function getComments(FileInfo $fileInfo, ReflectionClass $refClass, string $filePath): string
    {
        $ckOptions = CkOpt::make($this->options);
        $strMaxLen = ['get' => 0, 'set' => 0, 'var' => 0, 'apt' => 0, 'pro' => 0, 'proStr' => 0];
        $mapProModifiers = [
            ReflectionProperty::IS_PUBLIC => '[public]',
            ReflectionProperty::IS_PROTECTED => '[protected]',
            ReflectionProperty::IS_PRIVATE => '[private]',
        ];
        $ckOptions->checkRun(self::OPT_CREATE_CURRENT, function () use ($fileInfo, &$currentPropertyMath) {
            preg_match_all("/[ ]+(?:(?:public|protected|private)\s+)\\$([\S=]*).*;/", $fileInfo->getContent(), $outArr);
            $currentPropertyMath = ($outArr && isset($outArr[1])) ? $outArr[1] : [];
        });
        array_map(function (ReflectionProperty $property) use ($ckOptions, $currentPropertyMath, $mapProModifiers, &$setArr, &$getArr, &$aptArr, &$proArr, &$noting, &$strMaxLen) {
            /* ======== check ======== */
            if ($property->isStatic()
                || $ckOptions->check(self::PRO_NO_PUBLIC) && $property->isPublic()
                || $ckOptions->check(self::PRO_NO_PROTECTED) && $property->isProtected()
                || $ckOptions->check(self::PRO_NO_PRIVATE) && $property->isPrivate()) {
                goto end;
            }
            if ($currentPropertyMath && !in_array($property->getName(), $currentPropertyMath)) {
                goto end;
            }
            /* ======== comment ======== */
            $getVar = CommentUtil::matchCommentTag($this->tagVar, $property->getDocComment(), $this->defVar);
            $ucName = ucfirst($name = $property->getName());
            $setVar = $getVar === 'mixed' ? '' : $getVar . ' ';
            $proModifiers = $mapProModifiers[$property->getModifiers()];
            $proArr[$property->getModifiers()][$name] = ['var' => $getVar, 'property' => $proModifiers, 'str' => $proStr = " * @property \$var \$$name"];
            $setArr[$name] = $setStr = " * @method \$this set{$ucName}({$setVar}\${$name})";
            $getArr[$name] = ['var' => $getVar, 'str' => $getStr = " * @method \$var get{$ucName}()"];
            $aptArr[$name] = $aptStr = " * @method \$this|mixed {$name}({$setVar}\${$name} = null)";
            $noting[$name] = CommentUtil::matchCommentTag($this->tagNote, $property->getDocComment());
            
            /* ======== string max length ======== */
            $strMaxLen['var'] = ($varLen = strlen($getVar)) > $strMaxLen['var'] ? $varLen : $strMaxLen['var'];
            $strMaxLen['set'] = ($strLen = strlen($setStr)) > $strMaxLen['set'] ? $strLen : $strMaxLen['set'];
            $strMaxLen['get'] = ($strLen = strlen($getStr) - 4 + $strMaxLen['var']) > $strMaxLen['get'] ? $strLen : $strMaxLen['get'];
            $strMaxLen['apt'] = ($strLen = strlen($aptStr)) > $strMaxLen['apt'] ? $strLen : $strMaxLen['apt'];
            $strMaxLen['pro'] = ($proLen = strlen($proModifiers)) > $strMaxLen['pro'] ? $proLen : $strMaxLen['pro'];
            $strMaxLen['proStr'] = ($strLen = strlen($proStr) - 4 + $strMaxLen['var']) > $strMaxLen['proStr'] ? $strLen : $strMaxLen['proStr'];
            end:
        }, $refClass->getProperties());
        $swOptions = Criteria::opts($this->options);
        if ($strMaxLen) {
            /* ======== 属性生成排序 ======== */
            if ($ckOptions->check(self::COM_PRO)) {
                $proArr = array_reduce($proArr, function ($val1, $val2) {
                    return array_merge($val1, $val2);
                }, []);
            }
            /* ======== 处理 ======== */
            $_setFunc = function ($head, $comArr, $strLen) use ($noting, $strMaxLen, &$comment) {
                return function () use ($head, $comArr, $strLen, $strMaxLen, $noting, &$comment) {
                    $comment .= " * \n * ---------- $head ----------\n";
                    foreach ($comArr as $name => $content) {
                        is_array($content) && isset($content['property']) && $noting[$name] = str_pad($content['property'], $strMaxLen['pro'] + 1, ' ') . $noting[$name];
                        is_array($content) && strpos($content['str'], '$var') && $content = str_replace('$var', str_pad($content['var'], $strMaxLen['var'], ' '), $content['str']);
                        $comment .= ($noting[$name] ? str_pad($content, $strLen + 4, ' ') . $noting[$name] : $content) . "\n";
                    }
                };
            };
            /* ======== bind && handle ======== */
            $swOptions->bind(self::COM_PRO, $_setFunc('pro', $proArr, $strMaxLen['proStr']))
                ->bind(self::COM_SET, $_setFunc('set', $setArr, $strMaxLen['set']))
                ->bind(self::COM_GET, $_setFunc('get', $getArr, $strMaxLen['get']))
                ->bind(self::COM_APT, $_setFunc('apt', $aptArr, $strMaxLen['apt']))
                ->handle();
        }
        return $this->commentWithClassHead($fileInfo, ltrim(ltrim($comment ?? '', " *"), "\n"));
    }
}