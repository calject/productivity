<?php
/**
 * Author: 沧澜
 * Date: 2019-09-03
 */

namespace CalJect\Productivity\Components\Criteria;


use CalJect\Productivity\Components\Criteria\Branch\SwOption;
use CalJect\Productivity\Components\Criteria\Branch\SwBranch;
use CalJect\Productivity\Components\Criteria\Branch\BranchSwitchData;

class Criteria
{
    /**
     * @param int $opts
     * @return SwOption
     */
    public static function opts(int $opts = 0): SwOption
    {
        return SwOption::make($opts);
    }
    
    
    /**
     * @param mixed $checkValue
     * @return SwBranch
     */
    public static function switch($checkValue = null): SwBranch
    {
        return SwBranch::make($checkValue);
    }
    
    /**
     * @param mixed $checkValue
     * @param mixed $data
     * @return SwBranch
     */
    public static function switchData($checkValue = null, $data = []): SwBranch
    {
        $branch = SwBranch::make($checkValue);
        $branch->control()->setData($data);
        return $branch;
    }
    
}