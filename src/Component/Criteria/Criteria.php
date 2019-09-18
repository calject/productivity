<?php
/**
 * Author: 沧澜
 * Date: 2019-09-03
 */

namespace CalJect\Productivity\Component\Criteria;


use CalJect\Productivity\Component\Criteria\Branch\BranchOption;
use CalJect\Productivity\Component\Criteria\Branch\BranchSwitch;
use CalJect\Productivity\Component\Criteria\Branch\BranchSwitchData;

class Criteria
{
    /**
     * @param int $opts
     * @return BranchOption
     */
    public static function opts(int $opts = 0): BranchOption
    {
        return BranchOption::make($opts);
    }
    
    
    /**
     * @param mixed $checkValue
     * @return BranchSwitch
     */
    public static function switch($checkValue = null): BranchSwitch
    {
        return BranchSwitch::make($checkValue);
    }
    
    /**
     * @param mixed $checkValue
     * @param mixed $data
     * @return BranchSwitchData
     */
    public static function switchData($checkValue = null, $data = []): BranchSwitchData
    {
        return BranchSwitchData::make($checkValue)->with($data);
    }
    
}