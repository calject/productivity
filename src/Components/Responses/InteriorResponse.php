<?php
/**
 * Author: 沧澜
 * Date: 2019-09-19
 */

namespace CalJect\Productivity\Components\Responses;

use CalJect\Productivity\Contracts\Response\ResponseInterface;
use CalJect\Productivity\Contracts\Response\TInteriorResponse;
use Throwable;

class InteriorResponse implements ResponseInterface
{
    use TInteriorResponse;
    
    /**
     * @var mixed
     */
    protected $code;
    
    /**
     * @var string
     */
    protected $message;
    
    /**
     * @var mixed
     */
    protected $data;
    
    /**
     * @var array
     */
    protected $expand = [];
    
    /**
     * @var int
     */
    protected $httpStatusCode = 200;
    
    /**
     * @var Throwable
     */
    protected $exception;
    
    /**
     * @var bool
     */
    protected $isSuccess = false;
    
    /*---------------------------------------------- static ----------------------------------------------*/
    /**
     * @param int|string $code
     * @param string $message
     * @param mixed $data
     * @param mixed $expand
     * @return static
     */
    final public static function make($code, string $message, $data = [], $expand = [])
    {
        $instance = new static();
        $instance->setCode($code);
        $instance->setMessage($message);
        $instance->setData($data);
        $instance->setExpand($expand);
        $instance->setIsSuccess($code === 200);
        return $instance;
    }
    
    /**
     * @param string $message
     * @param array $data
     * @param array $expand
     * @return InteriorResponse
     */
    public static function success(string $message = 'success', $data = [], $expand = [])
    {
        return static::make(200, $message, $data, $expand)->setIsSuccess(true);
    }
    
    /**
     * @param int |string $code
     * @param string $message
     * @param mixed $data
     * @param mixed $expand
     * @return InteriorResponse
     */
    public static function error($code = 422, $message = 'error', $data = [], $expand = [])
    {
        return static::make($code, $message, $data, $expand)->setIsSuccess(false);
    }
    
    /**
     * @param int|string $code
     * @param string $message
     * @param mixed $data
     * @param mixed $expand
     * @return InteriorResponse
     */
    public static function successWithCode($code = 200, $message = 'success', $data = [], $expand = [])
    {
        return static::success($message, $data, $expand)->setCode($code);
    }
    
    /**
     * @param Throwable $throwable
     * @param mixed $data
     * @param mixed $expand
     * @return InteriorResponse
     */
    public static function exception(Throwable $throwable, $data = [], $expand = [])
    {
        return static::error($throwable->getCode(), $throwable->getMessage(), $data, $expand)->setExpand($throwable);
    }
    
    /*---------------------------------------------- check ----------------------------------------------*/
    
    /**
     * @return bool
     */
    public function check(): bool
    {
        return $this->isSuccess();
    }
    
    /**
     * @return bool
     */
    public function isSuccess(): bool
    {
        return $this->isSuccess;
    }
    
    /**
     * @return bool
     */
    public function isFailed(): bool
    {
        return !$this->isSuccess();
    }
    
    /**
     * when expand is array
     * @param string $key
     * @param mixed $default
     * @return mixed|null
     */
    public function expand(string $key, $default = null)
    {
        return $this->expand[$key] ?? $default;
    }
    
    /*---------------------------------------------- set ----------------------------------------------*/
    
    /**
     * @param mixed $code
     * @return $this
     */
    public function setCode($code)
    {
        $this->code = $code;
        return $this;
    }
    
    /**
     * @param string $message
     * @return $this
     */
    public function setMessage(string $message)
    {
        $this->message = $message;
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
     * @param mixed $expand
     * @return $this
     */
    public function setExpand($expand)
    {
        $this->expand = $expand;
        return $this;
    }
    
    /**
     * when expand is array or null
     * @param string $key
     * @param mixed $value
     * @return $this
     */
    public function appendExpand(string $key, $value)
    {
        $this->expand[$key] = $value;
        return $this;
    }
    
    /**
     * @param bool $isSuccess
     * @return $this
     */
    public function setIsSuccess(bool $isSuccess)
    {
        $this->isSuccess = $isSuccess;
        return $this;
    }
    
    /**
     * @param int $httpStatusCode
     * @return $this
     */
    public function setHttpStatusCode(int $httpStatusCode)
    {
        $this->httpStatusCode = $httpStatusCode;
        return $this;
    }
    
    /**
     * @param Throwable|null $exception
     * @return $this
     */
    public function setException($exception)
    {
        $this->exception = $exception;
        return $this;
    }
    
    /*---------------------------------------------- get ----------------------------------------------*/
    
    /**
     * @return mixed
     */
    public function getCode()
    {
        return $this->code;
    }
    
    /**
     * @return string
     */
    public function getMessage(): string
    {
        return $this->message;
    }
    
    /**
     * @return mixed
     */
    public function getData()
    {
        return $this->data;
    }
    
    /**
     * @return mixed
     */
    public function getExpand()
    {
        return $this->expand;
    }
    
    /**
     * @return int
     */
    public function getHttpStatusCode(): int
    {
        return $this->httpStatusCode;
    }
    
    /**
     * @return Throwable|null
     */
    public function getException()
    {
        return $this->exception;
    }
}