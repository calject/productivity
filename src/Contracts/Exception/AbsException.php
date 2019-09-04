<?php
/**
 * Author: 沧澜
 * Date: 2019-09-03
 */

namespace CalJect\Productivity\Contracts\Exception;

use Exception;
use Throwable;

/**
 * Class AbsException
 * @package CalJect\Productivity\Contracts
 */
class AbsException extends Exception
{
    /**
     * @param string $message
     * @param int $code
     * @param Throwable|null $previous
     * @throws static
     */
    public static function throw($message = "", $code = 0, Throwable $previous = null)
    {
        throw new static($message, $code, $previous);
    }
    
    /**
     * @param string $exceptionClass
     * @return mixed
     */
    public function convert(string $exceptionClass)
    {
        return new $exceptionClass($this->message, $this->code, $this->getPrevious());
    }
    
    /**
     * @param string $exceptionClass
     * @throws Exception
     */
    public function throwConvert(string $exceptionClass)
    {
        throw $this->convert($exceptionClass);
    }
}