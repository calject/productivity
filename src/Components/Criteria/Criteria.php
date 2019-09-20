<?php
/**
 * Author: 沧澜
 * Date: 2019-09-03
 */

namespace CalJect\Productivity\Components\Criteria;


use CalJect\Productivity\Components\Criteria\Branch\SwOption;
use CalJect\Productivity\Components\Criteria\Branch\SwBranch;
use CalJect\Productivity\Components\Criteria\Branch\BranchSwitchData;
use phpDocumentor\Reflection\Types\Self_;

class Criteria
{
    /* ======== SW OPT in SwControl ======== */
    const SW_OPT_BRANCH_PARAMS_CONTROL          = 1;
    const SW_OPT_BRANCH_PARAMS_VALUES           = 2;
    const SW_OPT_BRANCH_PARAMS_DATA             = 3;
    const SW_OPT_BRANCH_PARAMS_DATA_VALUES      = 4;
    
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
     * @param int $options
     * @return SwBranch
     */
    public static function switch($checkValue = null, int $options = 0): SwBranch
    {
        return SwBranch::make($checkValue, $options);
    }
    
    /**
     * @param mixed $checkValue
     * @param mixed $data
     * @param int $options
     * @return SwBranch
     */
    public static function switchData($checkValue = null, $data = [], int $options = 0): SwBranch
    {
        $branch = SwBranch::make($checkValue, $options);
        $branch->control()->setData($data);
        return $branch;
    }
    
    /**
     * @param null $checkValue
     * @param int $options
     * @return SwBranch
     */
    public static function newSwitchWithValue($checkValue = null, int $options = 0)
    {
        return self::switch($checkValue, $options);
    }
    
    /**
     * @param int $options
     * @return SwBranch
     */
    public static function newSwitchWithOptions(int $options = 0)
    {
        return SwBranch::make(null, $options);
    }
    
}