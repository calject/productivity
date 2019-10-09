<?php
/**
 * Author: 沧澜
 * Date: 2019-09-18
 */

namespace CalJect\Productivity\Components\Http\Service;


use CalJect\Productivity\Components\Criteria\Branch\SwBranch;
use CalJect\Productivity\Components\Criteria\Criteria;
use CalJect\Productivity\Components\Http\Client\HttpClient;
use CalJect\Productivity\Components\Http\Client\HttpRequest;
use CalJect\Productivity\Components\Http\Client\HttpResponse;
use CalJect\Productivity\Exceptions\ClosureRunException;
use Closure;

class HttpService
{
    
    /*
    |--------------------------------------------------------------------------
    | curl http请求返回状态码类型定义
    |--------------------------------------------------------------------------
    | 定义curl http请求状态码返回常量, 仅定义部分常用状态码
    |
    */
    const HTTP_CODE_100 = 100;      // HTTP 100
    const HTTP_CODE_200 = 200;      // HTTP 200
    const HTTP_CODE_500 = 500;      // HTTP 500
    const HTTP_CODE_502 = 502;      // HTTP 502
    const HTTP_CODE_504 = 504;      // HTTP 504
    const HTTP_CODE_403 = 403;      // HTTP 403
    const HTTP_CODE_404 = 404;      // HTTP 404
    const HTTP_CODE_422 = 422;      // HTTP 422
    const HTTP_CURL_ERROR = 0;      // curl 请求失败http状态码返回0
    const HTTP_CODE_OTHER = 9999;   // 其他HTTP状态码
    
    /**
     * http请求客户端
     * @var HttpClient
     */
    protected $client;
    
    /**
     * http状态码绑定处理
     * @var SwBranch
     */
    protected $httpBind;
    
    /**
     * 请求响应结果对象
     * @var HttpResponse
     */
    protected $httpResponse;
    
    
    public function __construct()
    {
        $this->client = new HttpClient();
        $this->httpBind = Criteria::newSwitchWithOptions(Criteria::SW_OPT_BRANCH_PARAMS_DATA_VALUES);
    }
    
    /*---------------------------------------------- http function ----------------------------------------------*/
    
    /**
     * 设置请求url,
     * @param string $url
     * @return $this
     */
    public function url(string $url)
    {
        $this->client->url($url);
        return $this;
    }
    
    /**
     * 设置请求头信息
     * @param array $header
     * @return $this
     */
    public function header(array $header)
    {
        $this->client->header($header);
        return $this;
    }
    
    /**
     * 设置请求数据
     * @param string|array $body
     * @return $this
     */
    public function body($body)
    {
        $this->client->body($body);
        return $this;
    }
    
    /**
     * 设置请求超时时间
     * @param int $timeout
     * @return $this
     */
    public function timeout(int $timeout)
    {
        $this->client->timeout($timeout);
        return $this;
    }
    
    /**
     * 设置请求方式 POST/GET/...
     * @param string $method
     * @return $this
     */
    public function method(string $method)
    {
        $this->client->method($method);
        return $this;
    }
    
    /**
     * 关闭curl资源
     * @return $this
     */
    public function close()
    {
        $this->client()->close();
        return $this;
    }
    
    /**
     * 设置是否将body参数使用http_build_query转化为字符串
     * @param bool $isBodyToString
     * @return $this
     */
    public function bodyToString(bool $isBodyToString)
    {
        $this->client->setClientOpt(HttpClient::OPTIONS_BODY_HTTP_BUILDER, $isBodyToString);
        return $this;
    }
    
    /**
     * 设置是否获取请求头信息
     * @param bool $isRequestHeaderOut
     * @return $this
     */
    public function requestHeaderOut(bool $isRequestHeaderOut)
    {
        $this->client->setClientOpt(HttpClient::OPTIONS_REQUEST_HEADER_OUT, $isRequestHeaderOut);
        return $this;
    }
    
    /**
     * 设置是否获取响应头信息
     * @param bool $isResponseHeaderOut
     * @return $this
     */
    public function responseHeaderOut(bool $isResponseHeaderOut)
    {
        $this->client->setClientOpt(HttpClient::OPTIONS_RESPONSE_HEADER_OUT, $isResponseHeaderOut);
        return $this;
    }
    
    /**
     * 执行http请求
     * @return HttpResponse|mixed  返回http响应结果 或者 对应的处理返回的结果(如果对应bind回调有返回的话)
     * @throws ClosureRunException
     */
    public function exec()
    {
        $httpResponse = $this->client->exec();
        $this->httpResponse = $httpResponse;
        $httpBind = $this->httpBind;
        $httpBind->isBindDefault() || $this->httpBind->default(function () use ($httpResponse) {
            return $httpResponse;
        });
        $httpBind->has(self::HTTP_CODE_200) || $this->success(function () use ($httpResponse) {
            return $httpResponse;
        });
        return $httpBind->send($httpResponse->getStatusCode(), $httpResponse)->handle();
    }
    
    /*---------------------------------------------- bind ----------------------------------------------*/
    
    /**
     * 获取当前http请求客户端对象
     * @return HttpClient
     */
    public function client()
    {
        return $this->client;
    }
    
    /**
     * @return HttpResponse
     */
    public function getHttpResponse(): HttpResponse
    {
        return $this->httpResponse;
    }
    
    /**
     * 是否已执行curl请求
     * @return bool
     */
    public function isExec()
    {
        return isset($this->httpResponse);
    }
    
    /**
     * 绑定http状态码处理
     * @param int $code
     * @param Closure $handle
     * 默认处理使用参数:
     *  function (HttpResponse $httpResponse) { }
     * 完整回调函数参数列表:
     *  // 参数1: http请求响应对象  参数2: http状态码  参数3: 默认参数回调 即error()绑定的回调  参数4: 所有绑定的回调列表
     *  function (HttpResponse $httpResponse, int $http_code, \Closure $default, array $binds) { }
     * @return $this
     */
    public function bind($code, Closure $handle)
    {
        if ($code === self::HTTP_CODE_OTHER) {
            $this->error($handle);
        }else {
            $this->httpBind->bind($code, $handle);
        }
        return $this;
    }
    
    /**
     * 绑定请求成功处理
     * @param Closure $successHandle
     * 默认处理使用参数:
     *  function (HttpResponse $httpResponse) { }
     * 完整回调函数参数列表:
     *  // 参数1: http请求响应对象  参数2: http状态码  参数3: 默认参数回调 即error()绑定的回调  参数4: 所有绑定的回调列表
     *  function (HttpResponse $httpResponse, int $http_code, \Closure $default, array $binds) { }
     * @return $this
     */
    public function success(Closure $successHandle)
    {
        return $this->bind(self::HTTP_CODE_200, $successHandle);
    }
    
    /**
     * 绑定请求失败处理
     * @param Closure $errorHandle
     * * 默认处理使用参数:
     *  function (HttpResponse $httpResponse, int $http_code) { }
     * 完整回调函数参数列表:
     *  // 参数1: http请求响应对象  参数2: http状态码  参数3: 所有绑定的回调列表
     *  function (HttpResponse $httpResponse, int $http_code, array $binds) { }
     * @return $this
     */
    public function error(Closure $errorHandle)
    {
        /* ======== 错误处理绑定到default处理 ======== */
        $this->httpBind->default($errorHandle);
        return $this;
    }
    
    /**
     * 绑定curl错误处理
     * @param Closure $curlErrorHandle
     * 默认处理使用参数:
     *  function (HttpResponse $httpResponse) { }
     * 完整回调函数参数列表:
     *  // 参数1: http请求响应对象  参数2: http状态码  参数3: 默认参数回调 即error()绑定的回调  参数4: 所有绑定的回调列表
     *  function (HttpResponse $httpResponse, int $http_code, \Closure $default, array $binds) { }
     * @return $this
     */
    public function curlError(Closure $curlErrorHandle)
    {
        return $this->bind(self::HTTP_CURL_ERROR, $curlErrorHandle);
    }
    
    /*---------------------------------------------- set ----------------------------------------------*/
    
    /**
     * 设置curl配置
     * @param mixed $key
     * @param mixed $value
     * @return $this
     */
    public function setCurlOpt($key, $value)
    {
        $this->client()->setCurlOpt($key, $value);
        return $this;
    }
    
    /**
     * 设置客户端配置参数
     * @param mixed $option
     * @param mixed $value
     * @return $this
     */
    public function setClientOpt($option, $value)
    {
        $this->client()->setClientOpt($option, $value);
        return $this;
    }
    
    /**
     * 设置请求结束是否关闭curl资源
     * @param bool $isExecCurlClose
     * @return $this
     */
    public function setExecCurlClose(bool $isExecCurlClose)
    {
        $this->client()->setExecCurlClose($isExecCurlClose);
        return $this;
    }
    
    /*---------------------------------------------- get ----------------------------------------------*/
    
    /**
     * 获取请求配置参数
     * @return HttpRequest
     */
    public function getRequest(): HttpRequest
    {
        return $this->client()->getRequest();
    }
    
    /**
     * 获取自定义配置参数
     * @return array
     */
    public function getOptions(): array
    {
        return $this->client()->getOptions();
    }
    
}