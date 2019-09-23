<?php
/**
 * Author: 沧澜
 * Date: 2019-09-20
 */

namespace CalJect\Productivity\Components\Result;

use CalJect\Productivity\Components\Check\CkOpt;
use CalJect\Productivity\Components\Responses\InteriorResponse;
use CalJect\Productivity\Components\Responses\InteriorResponseObject;
use CalJect\Productivity\Exceptions\ClosureRunException;
use CalJect\Productivity\Exceptions\TypeCheckException;
use CalJect\Productivity\Utils\ClosureUtil;
use Closure;
use Throwable;

/**
 * Class Result
 * @package CalJect\Productivity\Components\Result
 */
class Result
{
    
    const OPT_CONVERT_IN_SUCCESS = 1;
    const OPT_CONVERT_IN_ERROR   = 1 << 1;
    const OPT_CONVERT_IN_ALL     = self::OPT_CONVERT_IN_SUCCESS | self::OPT_CONVERT_IN_ERROR;
    
    /**
     * @var InteriorResponse
     */
    protected $interiorResponse;
    
    /**
     * @var string
     */
    protected $resultClass;
    
    /**
     * @var Closure
     */
    protected $through;
    
    /**
     * @var Closure
     */
    protected $convert;
    
    /**
     * 请求数据体
     * @var mixed
     */
    protected $requestData;
    
    /**
     * @var int
     */
    protected $options = self::OPT_CONVERT_IN_ALL;
    
    /**
     * @return InteriorResponse|mixed
     * @throws TypeCheckException
     * @throws ClosureRunException
     */
    public function exec()
    {
        $response = $this->interiorResponse;
        if ($this->resultClass instanceof InteriorResponseObject) {
            $response = $this->resultClass::convertByResponse($response);
            $throughResult = ClosureUtil::callNotNull($this->through, [$response, $this]);
            $response->setRequestObject($this->requestData);
            if ($response->getException() instanceof Throwable) {
                return $response;
            } else {
                CkOpt::make($this->options)->check($response->check() ? self::OPT_CONVERT_IN_SUCCESS : self::OPT_CONVERT_IN_ERROR) &&
                $response->setResponseObject(ClosureUtil::callNotNull($this->convert, [$response->getData(), $throughResult]));
            }
        } else if (!$this->resultClass) {
            TypeCheckException::throw('解析对象构建错误[is not subclass of InteriorResponseObject].');
        }
        return $response;
    }
    
    /**
     * @param Closure $convert
     * @return $this
     */
    public function convert(Closure $convert)
    {
        $this->convert = $convert;
        return $this;
    }
    
    /**
     * @param Closure $through
     * @return $this
     */
    public function through(Closure $through)
    {
        $this->through = $through;
        return $this;
    }
    
    /**
     * @param int $options
     * @return $this
     */
    public function options(int $options)
    {
        $this->options = $options;
        return $this;
    }
    
    /**
     * @param string $resultClass
     * @return $this
     */
    public function resultClass(string $resultClass)
    {
        $this->resultClass = $resultClass;
        return $this;
    }
}