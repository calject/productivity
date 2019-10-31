<?php
/**
 * Author: 沧澜
 * Date: 2019-10-31
 */

namespace CalJect\Productivity\Components\DataProperty;

use CalJect\Productivity\Components\Criteria\Criteria;
use CalJect\Productivity\Exceptions\ClosureRunException;
use CalJect\Productivity\Utils\CommentUtil;
use Closure;
use ReflectionProperty;

/**
 * Class CallDataPropertyOnClosure
 * @package CalJect\Productivity\Components\DataProperty
 */
class CallDataPropertyOnClosure extends CallDataProperty
{
    /**
     * 获取对象执行分类(默认使用closure)
     * @var string
     */
    protected $type = self::TYPE_CALL_CLOSURE;
    
    /**
     * call tag
     * @var string
     */
    protected $callTag = 'call';
    
    
    const TYPE_CALL_ON_TAG = 'other';       // 根据call[tag]注释类型执行closure
    const TYPE_CALL_NONE = 'none';          // 默认类型(不执行值回调,若值为closure类型将直接返回closure,不执行该函数)
    const TYPE_CALL_CLOSURE = 'closure';    // 直接执行closure函数
    
    /**
     * @param string $name
     * @param $value
     * @return mixed|void
     * @throws ClosureRunException
     * @throws Exception\VerifyException
     */
    public function _callGet(string $name, $value)
    {
        if ($value instanceof Closure) {
            $callBinds = $this->binds() + [
                self::TYPE_CALL_NONE => function ($value) { return $value; },
                self::TYPE_CALL_CLOSURE => $value
            ];
            $value = Criteria::switchData($this->type, $value)
                ->binds($callBinds)
                ->default(function ($value) use ($name, $callBinds) {
                    $refPro = new ReflectionProperty($this, $name);
                    if (($docComment = $refPro->getDocComment()) && $callType = CommentUtil::matchCommentTag('call', $docComment)) {
                        return Criteria::switch($callType)->binds($callBinds)->default($callBinds[self::TYPE_CALL_NONE])->handle();
                    } else {
                        return $value;
                    }
                })->handle();
            $this->_runCallSet($name, $value);
        }
        $this->throwVerifyException(static::class, $name, $value);
        return $value;
    }
    
    /**
     * 额外的绑定处理逻辑 [ $type => function () {} handle]
     * @return array
     */
    protected function binds(): array
    {
        return [];
    }
}