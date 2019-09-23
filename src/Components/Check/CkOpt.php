<?php
/**
 * Author: 沧澜
 * Date: 2019-09-03
 */

namespace CalJect\Productivity\Components\Check;

use CalJect\Productivity\Exceptions\ClosureRunException;
use CalJect\Productivity\Utils\ClosureUtil;
use Closure;

class CkOpt
{
    /**
     * @var int
     */
    protected $opts = 0;
    
    /**
     * OptCheck constructor.
     * @param int $opts
     */
    public function __construct(int $opts)
    {
        $this->opts = $opts;
    }
    
    /**
     * @param int $opts
     * @return static
     */
    public static function make(int $opts)
    {
        return new static($opts);
    }
    
    /**
     * @param int $check
     * @return bool
     */
    public function check(int $check): bool
    {
        return ($this->opts & $check) === $check;
    }
    
    /**
     * @param int $check
     * @param Closure|null $match
     * @param Closure|null $mismatch
     * @return mixed|null
     * @throws ClosureRunException
     */
    public function checkRun(int $check, Closure $match = null, Closure $mismatch = null)
    {
        if ($this->check($check)) {
            return ClosureUtil::callNotNull($match, [$this], true);
        } else {
            return ClosureUtil::callNotNull($mismatch, [$this], false);
        }
    }
    
    /**
     * @param mixed ...$args
     * @return bool
     */
    public function checkOr(... $args): bool
    {
        $args = (func_num_args() == 1 && is_array($args[0])) ? $args[0] : $args;
        $check = 0;
        foreach ($args as $arg) {
            $check |= $arg;
        }
        return (bool)($this->opts & $check);
    }
    
    /**
     * @param mixed ...$args
     * @return bool
     */
    public function checkAnd(... $args): bool
    {
        $args = (func_num_args() == 1 && is_array($args[0])) ? $args[0] : $args;
        $check = 0;
        foreach ($args as $arg) {
            $check |= $arg;
        }
        return ($this->opts & $check) === $check;
    }
    
    /**
     * @return int
     */
    public function getOpts(): int
    {
        return $this->opts;
    }
}