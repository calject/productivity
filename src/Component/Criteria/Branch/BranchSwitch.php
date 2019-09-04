<?php
/**
 * Author: 沧澜
 * Date: 2019-09-03
 */

namespace CalJect\Productivity\Component\Criteria\Branch;


use CalJect\Productivity\Contracts\Branch\AbsBranch;
use Closure;

class BranchSwitch extends AbsBranch
{
    /**
     * 检查值
     * @var mixed|Closure
     */
    protected $checkValue;
    
    /**
     * 绑定集
     * @var Closure[]
     */
    protected $binds = [];
    
    /**
     * 默认处理
     * @var Closure
     */
    protected $default;
    
    
    /**
     * Branch constructor.
     * @param mixed $checkValue    检查值
     */
    public function __construct($checkValue = null)
    {
        $this->checkValue = $checkValue;
        $this->init($checkValue);
    }
    
    /**
     * 初始化
     * @param mixed $checkValue
     */
    protected function init($checkValue)
    {
    
    }
    
    /**
     * 创建
     * @param mixed $checkValue
     * @return static
     */
    public static function make($checkValue = null)
    {
        return new static($checkValue);
    }
    
    /**
     * 设置检查值
     * @param mixed|Closure|string $checkValue
     * @return $this
     */
    public function send($checkValue)
    {
        $this->checkValue = $checkValue;
        return $this;
    }
    
    /**
     * 绑定处理
     * @param string|array $keys
     * @param Closure|string|array $handle   function($checkValue, [callable|string|null]$default, array $binds)
     *                                       闭包回调(\Closure)、执行的类方法(string[class::method | $class->$method])
     * @return $this
     */
    public function bind($keys, $handle)
    {
        if (is_array($keys)) {
            foreach ($keys as $item) {
                $this->bind($item, $handle);
            }
        } else {
            $this->binds[$keys] = $handle;
        }
        return $this;
    }
    
    /**
     * 绑定关系集
     * @param array $binds [$key => function($checkValue, [callable|string|null]$default, array $binds)]
     * @return $this
     */
    public function binds(array $binds)
    {
        $this->binds = $binds + $this->binds;
        return $this;
    }
    
    /**
     * 绑定默认处理
     * @param Closure|string|array $handle function($checkValue, array $binds)
     * @return $this
     */
    public function default($handle)
    {
        $this->default = $handle;
        return $this;
    }
    
    /**
     * 处理
     * @return mixed
     */
    public function handle()
    {
        $key = $this->checkClosureWithExec($this->checkValue) ?? $this->checkValue;
        if (isset($this->binds[$key])) {
            return $this->checkClosureWithExec($this->binds[$key], [$this->checkValue, $this->default, $this->binds]);
        } else if ($this->default) {
            return $this->checkClosureWithExec($this->default, [$this->checkValue, $this->binds]);
        } else {
            return $this->checkValue;
        }
    }
    
}