<?php
/**
 * Author: 沧澜
 * Date: 2019-09-17
 */

namespace CalJect\Productivity\Components\Http\Client;


use CalJect\Productivity\Components\Criteria\Branch\BranchSwitchData;
use CalJect\Productivity\Components\Criteria\Criteria;
use CalJect\Productivity\Components\Http\Curl\Curl;
use CalJect\Productivity\Components\Http\Curl\CurlInfo;

class HttpClient
{
    
    /*
    |--------------------------------------------------------------------------
    | options const
    |--------------------------------------------------------------------------
    | 当前client配置参数常量定义
    |
    */
    const OPTIONS_BODY_HTTP_BUILDER = 1001;  // body参数是否将数组转使用http_builder转换为字符串
    const OPTIONS_REQUEST_HEADER_OUT = 1002; // 是否获取请求头信息
    const OPTIONS_RESPONSE_HEADER_OUT = 1003;// 是否获取响应头信息
    
    /**
     * @var Curl
     */
    protected $curl;
    
    /**
     * 请求返回值
     * @var HttpResponse
     */
    protected $response;
    
    /**
     * curl info信息
     * @var CurlInfo
     */
    protected $info;
    
    /**
     * 配置参数
     * @var array
     */
    protected $options = [];
    
    /**
     * 请求参数
     * @var HttpRequest
     */
    protected $request;
    
    /**
     * request参数配置处理对象实例
     * @var BranchSwitchData
     */
    protected $requestHandle;
    
    /**
     * 配置参数处理对象实例
     * @var BranchSwitchData
     */
    protected $optionHandle;
    
    /**
     * HttpClient constructor.
     */
    public function __construct()
    {
        $this->init();
    }
    
    /**
     * @return $this
     */
    protected function newCurl()
    {
        $this->curl = new Curl();
        return $this;
    }
    
    /**
     * 初始化
     * @return void
     */
    protected function init()
    {
        /* ======== 初始化request参数配置处理实例(从设置的request参数按键值设置到curl请求配置中) ======== */
        $this->requestHandle = Criteria::switchData();
        $this->requestHandle->default(function ($value, $key) {
            // 调用Curl类的setXxx函数设置对应的参数值
            $this->curl->{'set'.ucfirst($key)}($value);
        });
        /* ======== 绑定body参数处理 ======== */
        $this->requestHandle->bind('body', function ($value, $key, $default) {
            // 自定义body参数设置的处理，然后交给default回调处理
            if ($this->optionValue(self::OPTIONS_BODY_HTTP_BUILDER)) {
                $value = is_array($value) ? http_build_query($value) : $value;
            }
            return $default($value, $key);
        });
        /* ======== 初始化配置参数处理实例并绑定相关配置项处理 ======== */
        $this->optionHandle = Criteria::switchData();
        $this->optionHandle->bind(self::OPTIONS_REQUEST_HEADER_OUT, function ($value) {
            $this->curl->setRequestHeaderOut($value);
        });
        $this->optionHandle->bind(self::OPTIONS_RESPONSE_HEADER_OUT, function ($value) {
            $this->curl->setResponseHeaderOut($value);
        });
        /* ======== 初始当前服务默认配置参数 ======== */
        $this->setClientOpt(self::OPTIONS_BODY_HTTP_BUILDER, true); // 设置默认将请求参数序列化为字符串
        $this->timeout(20);          // 设置默认请求超时为20s
        $this->method(Curl::POST);   // 设置默认为post请求
    }
    
    /*---------------------------------------------- client function ----------------------------------------------*/
    
    /**
     * 设置请求url,
     * @param string $url
     * @return $this
     */
    public function url(string $url)
    {
        $this->ckRequest()->setUrl($url);
        return $this;
    }
    
    /**
     * 设置请求头信息
     * @param array $header
     * @return $this
     */
    public function header(array $header)
    {
        $this->ckRequest()->setHeader($header);
        return $this;
    }
    
    /**
     * 设置请求数据
     * @param string|array $body
     * @return $this
     */
    public function body($body)
    {
        $this->ckRequest()->setBody($body);
        return $this;
    }
    
    /**
     * 设置请求超时时间(秒)
     * @param int $timeout
     * @return $this
     */
    public function timeout(int $timeout)
    {
        $this->ckRequest()->setTimeout($timeout);
        return $this;
    }
    
    /**
     * 设置请求方式 POST/GET/...
     * @param string $method
     * @return $this
     */
    public function method(string $method)
    {
        $this->ckRequest()->setMethod($method);
        return $this;
    }
    
    /**
     * 关闭curl资源
     * @return $this
     */
    public function close()
    {
        if ($this->curl) {
            $this->curl->close();
            $this->curl = null;
        }
        return $this;
    }
    
    /**
     * 执行curl 请求
     * @return HttpResponse
     */
    public function exec()
    {
        $this->close()->newCurl();
        $this->requestHandle();
        $this->optionHandle();
        /* ======== 执行curl请求 ======== */
        $curlResponse = $this->curl->execAndClose();
        /* ======== response ======== */
        return HttpResponse::createByCurlResponse($curlResponse)->setRequest($this->request);
    }
    
    /**
     * @param HttpRequest $request
     * @return HttpResponse
     */
    public function request(HttpRequest $request)
    {
        $this->request = $request;
        return $this->exec();
    }
    
    /**
     * @return $this
     */
    public function clear()
    {
        $this->close();
        $this->request = [];
        return $this;
    }
    
    /**
     * @return Curl
     */
    public function curl()
    {
        return $this->curl;
    }
    
    /*---------------------------------------------- set ----------------------------------------------*/
    
    /**
     * 设置当前客户端配置参数
     * @param mixed $option
     * @param mixed $value
     * @return $this
     */
    public function setClientOpt($option, $value)
    {
        $this->options[$option] = $value;
        return $this;
    }
    
    /**
     * 设置curl option
     * @param mixed $option
     * @param mixed $value
     * @return $this
     */
    public function setCurlOpt($option, $value)
    {
        $this->curl->setOpt($option, $value);
        return $this;
    }
    
    /**
     * 设置请求结束是否关闭curl资源
     * @param bool $isExecCurlClose
     * @return $this
     */
    public function setExecCurlClose(bool $isExecCurlClose)
    {
        $this->curl->setExecCurlClose($isExecCurlClose);
        return $this;
    }
    
    /**
     * @param HttpRequest $request
     * @return $this
     */
    public function setRequest(HttpRequest $request)
    {
        $this->request = $request;
        return $this;
    }
    
    /*---------------------------------------------- get ----------------------------------------------*/
    
    /**
     * 获取http请求响应(未请求则返回空对象)
     * @return HttpResponse
     */
    public function getResponse(): HttpResponse
    {
        return $this->response ?? new HttpResponse();
    }
    
    /**
     * 获取配置参数
     * @return array
     */
    public function getOptions(): array
    {
        return $this->options;
    }
    
    /**
     * 获取请求配置参数
     * @return HttpRequest
     */
    public function getRequest(): HttpRequest
    {
        return $this->ckRequest();
    }
    
    /*---------------------------------------------- protected ----------------------------------------------*/
    
    /**
     * @return HttpRequest
     */
    protected function ckRequest()
    {
        if (!isset($this->request)) {
            $this->request = new HttpRequest();
        }
        return $this->request;
    }
    
    /**
     * 获取配置参数(自定义配置参数)
     * @param string $option
     * @param bool|mixed $default
     * @return mixed|null
     */
    protected function optionValue($option, $default = false)
    {
        return $this->options[$option] ?? $default;
    }
    
    /**
     * request参数配置处理
     * @return $this
     */
    protected function requestHandle()
    {
        /* ======== 设置请求参数配置 ======== */
        foreach ($this->request->toArray() as $option => $value) {
            $this->requestHandle->send($option)->with($value)->handle();
        }
        return $this;
    }
    
    /**
     * 配置参数处理
     * @return $this
     */
    protected function optionHandle()
    {
        foreach ($this->options as $option => $value) {
            $this->optionHandle->send($option)->with($value)->handle();
        }
        return $this;
    }
    
    public function __destruct()
    {
        $this->clear();
    }
    
}