<?php
/**
 * Author: 沧澜
 * Date: 2019-09-03
 */

namespace CalJect\Productivity\Components\Criteria\Branch;

use CalJect\Productivity\Exceptions\ClosureRunException;
use CalJect\Productivity\Utils\ClosureUtil;
use CalJect\Productivity\Utils\SCkOpt;

class SwOption extends SwBranch
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
    
    /**
     * handle
     * @return mixed
     * @throws ClosureRunException
     */
    public function handle()
    {
        $control = $this->control;
        $checkValue = $control->getCheckValue();
        $opts = ClosureUtil::checkClosureWithExec($checkValue, [$control], false, $checkValue);
        foreach ($control->getBinds() as $opt => $closure) {
            if (SCkOpt::check($opts, $opt)) {
                ClosureUtil::checkClosureWithExec($closure, [$control->getData(), $opt]);
                $_ck = true;
            }
        }
        if ($opts === 0 || !isset($_ck)) {
            $control->callDefault();
        }
    }
}