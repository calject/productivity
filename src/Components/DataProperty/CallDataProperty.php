<?php
/**
 * Author: 沧澜
 * Date: 2019-10-23
 */

namespace CalJect\Productivity\Components\DataProperty;

use CalJect\Productivity\Components\DataProperty\Exception\VerifyException;
use CalJect\Productivity\Utils\CommentUtil;
use Closure;

/**
 * Class CallDataProperty
 * @package CalJect\Productivity\Components\DataProperty
 * @method mixed _callSet(string $name, $arguments)
 * @method mixed _callGet(string $name, $value)
 * @method mixed _callOther(string $name, $arguments)
 */
abstract class CallDataProperty
{
    /**
     * @var bool
     */
    private $isCallSet, $isCallGet, $isCallOther = false;
    
    /**
     * CallDataProperty constructor.
     */
    public function __construct()
    {
        $this->isCallSet = method_exists($this, '_callSet');
        $this->isCallGet = method_exists($this, '_callGet');
        $this->isCallOther = method_exists($this, '_callOther');
        $this->_init();
    }
    // class create init
    protected function _init() { }
    
    /**
     * @param string $name
     * @param mixed $arguments
     * @return $this|mixed
     */
    public function __call($name, $arguments)
    {
        $method = substr($name, 0, 3);
        $property = lcfirst(substr($name, 3));
        if ($method == 'set') {
            $_ = $this->isCallSet ? $this->_callSet($property, $arguments) : ($this->{$property} = $arguments[0] ?? null);
            return $this;
        } elseif ($method == 'get') {
            return $this->isCallGet ? $this->_callGet($property, $this->{$property}) : $this->{$property};
        } else {
            if ($this->isCallOther) {
                return $this->_callOther($name, $arguments);
            }
        }
    }
    
    /**
     * @param array $data
     * @param null $default
     * @return $this
     */
    public function setInArray(array $data, $default = null)
    {
        foreach ($this as $property => $value) {
            $this->{$property} = $data[$property] ?? $this->{$property} ?? $default;
        }
        return $this;
    }
    
    /**
     * @param Closure $handle
     * @param array $data
     * @return $this
     */
    public function setMap(Closure $handle, array $data)
    {
        foreach ($this as $property => $value) {
            call_user_func_array($handle,[$this, $property, $data[$property] ?? null, $value]);
        }
        return $this;
    }
    
    /*---------------------------------------------- protected ----------------------------------------------*/
    
    /**
     * @param string $name
     * @param mixed $value
     * @return $this|mixed
     */
    protected function _runCallSet(string $name, $value)
    {
        return $this->{'set' . ucfirst($name)}($value);
    }
    
    /**
     * @param string $name
     * @return mixed
     */
    protected function _runCallGet(string $name)
    {
        return $this->{'get' . ucfirst($name)}();
    }
    
    /**
     * @param string $class
     * @param string $name
     * @param string $docComment
     * @param string $checkTag
     * @throws VerifyException
     */
    protected function throwVerifyException(string $class, string $name, string $docComment, $checkTag = 'note')
    {
        if ($note = CommentUtil::matchCommentTag($checkTag, $docComment)) {
            VerifyException::throw("{$note}[$name] 字段不能为空.", 422);
        } else {
            VerifyException::throw(basename(str_replace('\\', '/', $class)) . "::$name 属性不能为空.", 422);
        }
    }
}