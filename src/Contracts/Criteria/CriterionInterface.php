<?php
/**
 * Author: 沧澜
 * Date: 2019-09-03
 */

namespace CalJect\Productivity\Contracts\Criteria;

/**
 * Interface CriteriaInterface
 * @package CalJect\Productivity\Contracts\Criteria
 */
interface CriterionInterface
{
    /**
     * @return bool
     */
    public function check(): bool;
    
}