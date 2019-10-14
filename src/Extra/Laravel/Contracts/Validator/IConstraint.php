<?php
/**
 * Author: 沧澜
 * Date: 2019-10-14
 */

namespace CalJect\Productivity\Extra\Laravel\Contracts\Validator;

/**
 * Interface IConstraint
 * @package CalJect\Productivity\Extra\Laravel\Contracts\Validator
 */
interface IConstraint
{
    /**
     * @param mixed ...$args
     * @return array
     */
    public function getRules(... $args): array;
}