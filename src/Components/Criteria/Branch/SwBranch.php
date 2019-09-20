<?php
/**
 * Author: 沧澜
 * Date: 2019-09-03
 */

namespace CalJect\Productivity\Components\Criteria\Branch;


use CalJect\Productivity\Contracts\Branch\AbsBranch;
use CalJect\Productivity\Exceptions\ClosureRunException;
use CalJect\Productivity\Utils\ClosureUtil;
use Closure;

class SwBranch extends AbsBranch
{
    /**
     * @var SwControl
     */
    protected $control;
    
    /**
     * Branch constructor.
     * @param mixed $checkValue 检查值
     * @param int $options
     */
    public function __construct($checkValue = null, int $options = 0)
    {
        $this->control = new SwControl($options);
        $this->control->setCheckValue($checkValue);
        $this->init($checkValue, $this->control);
    }
    
    /**
     * 初始化
     * @param mixed $checkValue
     * @param SwControl $control
     */
    protected function init($checkValue, SwControl $control)
    {
    
    }
    
    /**
     * 创建
     * @param mixed $checkValue
     * @param int $options
     * @return static
     */
    public static function make($checkValue = null, int $options = 0)
    {
        return new static($checkValue, $options);
    }
    
    /**
     * 设置检查值
     * @param mixed|Closure|string $checkValue
     * @param mixed $data
     * @return $this
     */
    public function send($checkValue, $data = null)
    {
        $this->control->setCheckValue($checkValue)->setData($data);
        return $this;
    }
    
    /**
     * 绑定处理
     * @param string|array $keys
     * @param Closure|string|array $handle function($checkValue, [callable|string|null]$default, array $binds)
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
            $this->control->appendBind($keys, $handle);
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
        $this->control->setBinds($binds);
        return $this;
    }
    
    /**
     * 绑定默认处理
     * @param Closure|string|array $handle function($checkValue, array $binds)
     * @return $this
     */
    public function default($handle)
    {
        $this->control->setDefault($handle);
        return $this;
    }
    
    /**
     * 处理
     * @return mixed
     * @throws ClosureRunException
     */
    public function handle()
    {
        $checkValue = $this->control->getCheckValue();
        $key = ClosureUtil::checkClosureWithExec($checkValue, [$this->control], false, $checkValue);
        return $this->control->callInDefault($key);
    }
    
    /**
     * @return SwControl
     */
    public function control()
    {
        return $this->control;
    }
    
    /**
     * @param int $options
     * @return $this
     */
    public function setControlOptions(int $options)
    {
        $this->control->setOptions($options);
        return $this;
    }
    
    /**
     * @return mixed
     * @throws ClosureRunException
     */
    public function __invoke()
    {
        return $this->handle();
    }
}