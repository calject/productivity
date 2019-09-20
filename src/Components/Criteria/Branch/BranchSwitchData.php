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
     * 设置回调附加的数据体
     * @param mixed $data
     * @return $this
     */
    public function with($data)
    {
        $this->control->setData($data);
        return $this;
    }
}