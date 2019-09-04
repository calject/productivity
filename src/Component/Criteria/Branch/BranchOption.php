<?php
/**
 * Author: 沧澜
 * Date: 2019-09-03
 */

namespace CalJect\Productivity\Component\Criteria\Branch;

use CalJect\Productivity\Utils\SCkOpt;
use Closure;

class BranchOption extends BranchSwitch
{
    /**
     * 附加数据体
     * @var mixed
     */
    protected $data = [];
    
    /**
     * @param mixed $checkValue
     */
    protected function init($checkValue)
    {
        $this->closureBindTo = $this;
    }
    
    /**
     * 设置回调附加的数据体
     * @param mixed $data
     * @return $this
     */
    public function with($data)
    {
        $this->data = $data;
        return $this;
    }
    
    /**
     * handle
     * @return mixed
     */
    public function handle()
    {
        $opts = $this->checkClosureWithExec($this->checkValue) ?? $this->checkValue;
        foreach ($this->binds as $opt => $closure) {
            if (SCkOpt::check($opts, $opt)) {
                $this->call($closure);
                $_ck = true;
            }
        }
        if ($opts === 0 || !isset($_ck)) {
            $this->callDefault();
        }
    }
    
    /**
     * @param Closure $closure
     * @return mixed
     */
    protected function call(Closure $closure)
    {
        return $this->checkClosureWithExec($closure, $this->data);
    }
    
    /**
     * @param string $key
     * @return mixed
     */
    protected function callIn(string $key)
    {
        return $this->call($this->binds[$key]);
    }
    
    /**
     * @return mixed
     */
    protected function callDefault()
    {
        return $this->call($this->default);
    }
}