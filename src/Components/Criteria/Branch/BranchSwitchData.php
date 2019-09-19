<?php
/**
 * Author: 沧澜
 * Date: 2019-09-03
 */

namespace CalJect\Productivity\Components\Criteria\Branch;

/**
 * Class BranchSwitchData
 * @package CalJect\Productivity\Components\Criteria\Branch
 */
class BranchSwitchData extends BranchSwitch
{
    /**
     * 附加数据体
     * @var mixed
     */
    protected $data;
    
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
     * 处理
     * @return mixed
     */
    public function handle()
    {
        $key = $this->checkClosureWithExec($this->checkValue) ?? $this->checkValue;
        if (isset($this->binds[$key])) {
            return $this->checkClosureWithExec($this->binds[$key], [$this->data, $this->checkValue, $this->default, $this->binds]);
        } else if (isset($this->default)) {
            return $this->checkClosureWithExec($this->default, [$this->data, $this->checkValue, $this->binds]);
        }else {
            return null;
        }
    }
}