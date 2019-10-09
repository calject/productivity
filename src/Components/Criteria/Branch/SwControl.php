<?php
/**
 * Author: 沧澜
 * Date: 2019-09-20
 */

namespace CalJect\Productivity\Components\Criteria\Branch;


use CalJect\Productivity\Components\Criteria\Criteria;
use CalJect\Productivity\Exceptions\ClosureRunException;
use CalJect\Productivity\Utils\ClosureUtil;
use Closure;

class SwControl
{
    /**
     * @var mixed
     */
    protected $checkValue;
    
    /**
     * @var Closure
     */
    protected $default;
    
    /**
     * @var array
     */
    protected $binds = [];
    
    /**
     * @var mixed
     */
    protected $data;
    
    /**
     * @var int
     */
    protected $options = 0;
    
    /**
     * SwControl constructor.
     * @param int $options
     */
    public function __construct(int $options = 0)
    {
        $this->options = $options;
    }
    
    /*---------------------------------------------- call ----------------------------------------------*/
    /**
     * @param array $params
     * @return mixed
     * @throws ClosureRunException
     */
    public function callDefault(array $params = [])
    {
        if (isset($this->default)) {
            return ClosureUtil::checkClosureWithExec($this->default, $params ?: $this->getParams());
        } else {
            return $this->checkValue;
        }
    }
    
    /**
     * @param int|string $key
     * @param Closure $noFoundHandle
     * @return mixed
     * @throws ClosureRunException
     */
    public function call($key, Closure $noFoundHandle = null)
    {
        if (isset($this->binds[$key])) {
            return ClosureUtil::checkClosureWithExec($this->binds[$key], [$this]);
        } else {
            return $noFoundHandle ? ClosureUtil::checkClosureWithExec($noFoundHandle, $this->getParams()) : null;
        }
    }
    
    /**
     * @param int|string $key
     * @return mixed
     * @throws ClosureRunException
     */
    public function callInDefault($key)
    {
        if (isset($this->binds[$key])) {
            return ClosureUtil::checkClosureWithExec($this->binds[$key], $this->getParams());
        } else {
            return $this->callDefault();
        }
    }
    
    /**
     * @return Closure
     */
    public static function CLOSURE_DEFAULT(): Closure
    {
        return function (SwControl $control) {
            return $control->callDefault();
        };
    }
    
    /**
     * @param string|int $key
     * @return Closure
     */
    public static function CLOSURE_BIND($key): Closure
    {
        return function (SwControl $control) use ($key) {
            return $control->callInDefault($key);
        };
    }
    /*---------------------------------------------- check ----------------------------------------------*/
    
    /**
     * @param string|int $key
     * @return bool
     */
    public function has($key): bool
    {
        return isset($this->binds[$key]);
    }
    
    /**
     * @return bool
     */
    public function isBindHandle(): bool
    {
        return $this->binds && $this->default;
    }
    
    /**
     * @return bool
     */
    public function isBindBinds(): bool
    {
        return (bool)$this->binds;
    }
    
    /**
     * @return bool
     */
    public function isBindDefault(): bool
    {
        return (bool)$this->default;
    }
    
    /**
     * @return array
     */
    protected function getParams(): array
    {
        switch ($this->options) {
            case Criteria::SW_OPT_BRANCH_PARAMS_VALUES:
                return [$this->checkValue, $this];
            case Criteria::SW_OPT_BRANCH_PARAMS_DATA:
                return [$this->data, $this];
            case Criteria::SW_OPT_BRANCH_PARAMS_DATA_VALUES:
                return [$this->data, $this->checkValue, $this];
            case Criteria::SW_OPT_BRANCH_PARAMS_CONTROL:
            default:
                return [$this];
        }
    }
    
    /*---------------------------------------------- set/get ----------------------------------------------*/
    
    /**
     * @param mixed $checkValue
     * @return $this
     */
    public function setCheckValue($checkValue)
    {
        $this->checkValue = $checkValue;
        return $this;
    }
    
    /**
     * @param Closure $default
     * @return $this
     */
    public function setDefault(Closure $default)
    {
        $this->default = $default;
        return $this;
    }
    
    /**
     * @param array $binds
     * @return $this
     */
    public function setBinds(array $binds)
    {
        $this->binds = $binds;
        return $this;
    }
    
    /**
     * @param string|int $key
     * @param mixed $value
     * @return $this
     */
    public function appendBind($key, $value)
    {
        $this->binds[$key] = $value;
        return $this;
    }
    
    /**
     * @param mixed $data
     * @return $this
     */
    public function setData($data)
    {
        $this->data = $data;
        return $this;
    }
    
    /**
     * @param int $options
     * @return $this
     */
    public function setOptions(int $options)
    {
        $this->options = $options;
        return $this;
    }
    
    /*---------------------------------------------- get ----------------------------------------------*/
    
    /**
     * @return mixed
     */
    public function getCheckValue()
    {
        return $this->checkValue;
    }
    
    /**
     * @return Closure
     */
    public function getDefault(): Closure
    {
        return $this->default;
    }
    
    /**
     * @return array
     */
    public function getBinds(): array
    {
        return $this->binds;
    }
    
    /**
     * @return mixed
     */
    public function getData()
    {
        return $this->data;
    }
    
    /**
     * @return int
     */
    public function getOptions(): int
    {
        return $this->options;
    }
}