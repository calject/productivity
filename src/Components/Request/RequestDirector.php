<?php
/**
 * Author: 沧澜
 * Date: 2019-09-20
 */

namespace CalJect\Productivity\Components\Request;


use CalJect\Productivity\Components\Http\Client\HttpResponse;
use CalJect\Productivity\Components\Http\Service\HttpService;
use CalJect\Productivity\Components\Responses\InteriorResponse;
use CalJect\Productivity\Contracts\Request\RequestBuilder;
use CalJect\Productivity\Exceptions\ClosureRunException;
use CalJect\Productivity\Utils\ClosureUtil;
use CalJect\Productivity\Utils\TimeUtil;
use Closure;
use Throwable;

class RequestDirector
{
    /**
     * @var RequestBuilder
     */
    protected $requestBuilder;
    
    /**
     * @var Closure
     */
    protected $setting;
    
    /**
     * @var Closure
     */
    protected $finally;
    
    /**
     * Request constructor.
     * @param $builder
     */
    public function __construct(RequestBuilder $builder)
    {
        $this->requestBuilder = $builder;
    }
    
    /**
     * @param mixed $options
     * @param array $params
     * @return InteriorResponse
     * @throws ClosureRunException
     */
    public function exec($options, array $params): InteriorResponse
    {
        $httpService = new HttpService();
        $builder = $this->getBuilder();
        $_startTime = TimeUtil::getTimeMillisecondWhole();
        try {
            $url = $builder->url($options);
            $body = $builder->body($options, $params);
            $header = $builder->header($options, $body);
            $httpService->url($url)->header($header)->body($body);
            $httpService->success(function (HttpResponse $httpResponse) use ($options, $builder) {
                return $builder->parse($options, $httpResponse->getResponseData());
            })->error(function (HttpResponse $response, $err_code) {
                return InteriorResponse::error(0, 'http请求失败:' . $err_code, ["err_code" => $err_code, "err_msg" => 'http error with ' . $err_code]);
            })->curlError(function (HttpResponse $response) {
                return InteriorResponse::error(0, 'curl请求失败:' . $response->getCurlErrorNo(),
                    ["err_code" => $response->getCurlErrorNo(), "err_msg" => $response->getCurlErrorMsg()]);
            });
            ClosureUtil::callNotNull($this->setting, [$httpService]);
            return $httpService->exec();
        } catch (Throwable $throwable) {
            return InteriorResponse::exception($throwable);
        } finally {
            $_endTime = TimeUtil::getTimeMillisecondWhole();
            $_timeDiff = $_endTime - $_startTime;
            ClosureUtil::callNotNull($this->finally, [$httpService, $_timeDiff]);
        }
    }
    
    /**
     * @param Closure $setting function(HttpService $service) {}
     * @return $this
     */
    public function httpSet(Closure $setting)
    {
        $this->setting = $setting;
        return $this;
    }
    
    /**
     * @param Closure $finally
     * @return $this
     */
    public function finally(Closure $finally)
    {
        $this->finally = $finally;
        return $this;
    }
    
    /**
     * @return RequestBuilder
     */
    public function getBuilder()
    {
        return $this->requestBuilder;
    }
    
}