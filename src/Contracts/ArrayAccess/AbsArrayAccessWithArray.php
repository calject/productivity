<?php
/**
 * Author: 沧澜
 * Date: 2019-09-03
 */

namespace CalJect\Productivity\Contracts\ArrayAccess;

use ArrayAccess;
use CalJect\Productivity\Contracts\ToArray;

/**
 * Class AbsArrayAccessWithArray
 * @package CalJect\Productivity\Contracts\ArrayAccess
 */
abstract class AbsArrayAccessWithArray implements ArrayAccess, ToArray
{
    /**
     * @var array
     */
    private $arrayAccessData = [];
    
    /**
     * @var null
     */
    private $default = null;
    
    /**
     * 设置访问的数据
     * @param array $arrayAccessData
     * @return AbsArrayAccessWithArray
     */
    public function setArrayAccessData(array $arrayAccessData)
    {
        $this->arrayAccessData = $arrayAccessData;
        return $this;
    }
    
    /**
     * 设置访问为空默认数据
     * @param mixed $default
     * @return $this
     */
    public function setDefault($default)
    {
        $this->default = $default;
        return $this;
    }
    
    /**
     * Whether a offset exists
     * @link http://php.net/manual/en/arrayaccess.offsetexists.php
     * @param mixed $offset <p>
     * An offset to check for.
     * </p>
     * @return boolean true on success or false on failure.
     * </p>
     * <p>
     * The return value will be casted to boolean if non-boolean was returned.
     * @since 5.0.0
     */
    public function offsetExists($offset)
    {
        return isset($this->arrayAccessData[$offset]);
    }
    
    /**
     * Offset to retrieve
     * @link http://php.net/manual/en/arrayaccess.offsetget.php
     * @param mixed $offset <p>
     * The offset to retrieve.
     * </p>
     * @return mixed Can return all value types.
     * @since 5.0.0
     */
    public function offsetGet($offset)
    {
        return $this->arrayAccessData[$offset] ?? $this->default;
    }
    
    /**
     * Offset to set
     * @link http://php.net/manual/en/arrayaccess.offsetset.php
     * @param mixed $offset <p>
     * The offset to assign the value to.
     * </p>
     * @param mixed $value <p>
     * The value to set.
     * </p>
     * @return void
     * @since 5.0.0
     */
    public function offsetSet($offset, $value)
    {
        $this->arrayAccessData[$offset] = $value;
    }
    
    /**
     * Offset to unset
     * @link http://php.net/manual/en/arrayaccess.offsetunset.php
     * @param mixed $offset <p>
     * The offset to unset.
     * </p>
     * @return void
     * @since 5.0.0
     */
    public function offsetUnset($offset)
    {
        unset($this->arrayAccessData[$offset]);
    }
    
    /**
     * @return array
     */
    public function toArray(): array
    {
        return $this->arrayAccessData;
    }
    
    /**
     * @return array
     */
    public function __invoke()
    {
        return $this->toArray();
    }
    
    /**
     * @return false|string
     */
    public function __toString()
    {
        return json_encode($this->arrayAccessData, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
    }
    
}