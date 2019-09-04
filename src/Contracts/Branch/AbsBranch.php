<?php
/**
 * Author: 沧澜
 * Date: 2019-09-03
 */

namespace CalJect\Productivity\Contracts\Branch;


use Closure;

class AbsBranch
{
    
    /**
     * 回调函数绑定作用域
     * @var mixed
     */
    protected $closureBindTo = null;
    
    /**
     * 设置回调函数绑定作用域
     * @param mixed $closureBindTo
     * @return $this
     */
    public function setClosureBindTo($closureBindTo)
    {
        $this->closureBindTo = $closureBindTo;
        return $this;
    }
    
    /**
     * @param Closure $closure 执行的匿名函数
     * @param array $params     参数列表数组
     * @return mixed
     */
    protected function execClosure(Closure $closure, array $params = [])
    {
        if (isset($this->closureBindTo)) {
            return $closure->call($this->closureBindTo, ... $params);
        }else {
            return $closure(... $params);
        }
    }
    
    /**
     * 检查的闭包函数或执行类 并执行回调
     * @param Closure|string|array $closure   检查的闭包函数或执行类(string/array)
     * @param array $params
     * @return mixed
     */
    protected function checkClosureWithExec($closure, array $params = [])
    {
        if ($closure instanceof Closure) {
            return $this->execClosure($closure, $params);
        } else if (is_array($closure) && count($closure) >= 2) {
            return call_user_func_array(array_slice($closure, 0, 2), $params);
        } else if (is_string($closure)) {
            return $this->checkClosureWithExec(explode("::", $closure), $params);
        } else {
            return null;
        }
    }
}