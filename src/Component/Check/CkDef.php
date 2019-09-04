<?php
/**
 * Author: 沧澜
 * Date: 2019-09-03
 */

namespace CalJect\Productivity\Component\Check;

use CalJect\Productivity\Contracts\ArrayAccess\AbsArrayAccessWithArray;

class CkDef extends AbsArrayAccessWithArray
{
    /**
     * CheckData constructor.
     * @param array $data
     * @param string $default
     */
    public function __construct(array $data = [], $default = null)
    {
        $this->setArrayAccessData($data);
        $this->setDefault($default);
    }
    
    /**
     * @param array $data
     * @param string $default
     * @return static
     */
    public static function make(array $data = [], $default = null)
    {
        return new self($data, $default);
    }
    
    /**
     * @param string $key
     * @param mixed $default
     * @return mixed|null
     */
    public function get(string $key, $default = null)
    {
        return $this[$key] ?? $default;
    }
    
    /**
     * @return array
     */
    public function all()
    {
        return $this->toArray();
    }
}