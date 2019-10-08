<?php
/**
 * Author: 沧澜
 * Date: 2019-09-16
 */

namespace CalJect\Productivity\Components\Http\Curl;

use CalJect\Productivity\Components\Criteria\Criteria;

class Curl
{
    const HEAD      = 'HEAD';
    const GET       = 'GET';
    const POST      = 'POST';
    const PUT       = 'PUT';
    const DELETE    = 'DELETE';
    const PATCH     = 'PATCH';
    const OPTIONS   = 'OPTIONS';
    const TRACE     = 'TRACE';
    
    const CURL_BODY_BODY_PARSER     = 1;        // body数据解析
    const CURL_BODY_GET_PARSER      = 1 << 1;   // get参数解析
    
    /**
     * 请求地址
     * @var string
     */
    protected $url;
    
    /**
     * resource a cURL handle | curl资源操作符
     * @var resource
     */
    protected $ch;
    
    /**
     * curl资源是否被释放
     * @var bool
     */
    protected $is_curl_close = false;
    
    /**
     * 执行请求完成是否释放curl资源
     * @var bool
     */
    protected $exec_curl_close = true;
    
    /**
     * @var CurlResponse
     */
    protected $response;
    
    /**
     * 配置参数
     * @var array
     */
    protected $options = [];
    
    /**
     * @var int
     */
    protected $execOpts = 0;
    
    /**
     * Curl constructor.
     * @param string $url 请求地址 | default: null
     */
    public function __construct($url = null)
    {
        $this->url = $url;
        $this->ch = curl_init($this->url);
        $this->init();
    }
    
    /**
     * 初始化
     * @return $this
     */
    public function init()
    {
        /* ======== 执行结果:0.返回 1.不返回 ======== */
        $this->setOpt(CURLOPT_RETURNTRANSFER, true);
        /* ======== 是否显示头部信息 0.不显示 1.显示 ======== */
        $this->setOpt(CURLOPT_HEADER, false);
        /* ======== 配置忽略https证书检查 ======== */
        if (1 === strpos("$" . $this->url, "https://")) {
            $this->setOpt(CURLOPT_SSL_VERIFYPEER, false);
            $this->setOpt(CURLOPT_SSL_VERIFYHOST, false);
        }
        $this->execOpts = self::CURL_BODY_BODY_PARSER | self::CURL_BODY_GET_PARSER;
        return $this;
    }
    
    /**
     * 配置参数
     * @param mixed $option
     * @param mixed $value
     * @return $this
     */
    protected function builder($option, $value)
    {
        $this->option($option, $value);
        return $this;
    }
    
    /**
     * 设置/获取 指定key的值 ($value为null时为设置值，否则为获取该值, 这里规定 $configure 数组内的值 不允许为null值)
     * @param string $key
     * @param null|mixed $value
     * @return mixed
     */
    protected function option($key, $value = null)
    {
        return isset($value) ? $this->options[$key] = $value : ($this->options[$key] ?? false);
    }
    
    
    /*---------------------------------------------- set options ----------------------------------------------*/
    
    
    /**
     * @param int $execOpts
     * @return $this
     */
    public function setExecOpts(int $execOpts)
    {
        $this->execOpts = $execOpts;
        return $this;
    }
    
    /**
     * 设置curl option
     * @param mixed $option
     * @param mixed $value
     * @return $this
     */
    public function setOpt($option, $value)
    {
        curl_setopt($this->ch, $option, $value);
        return $this->builder($option, $value);
    }
    
    /**
     * 设置请求地址
     * @param string $url
     * @return Curl
     */
    public function setUrl($url)
    {
        $this->url = $url;
        return $this->setOpt(CURLOPT_URL, $url);
    }
    
    /**
     * 设置请求头信息
     * @param array $header
     * @return Curl
     */
    public function setHeader(array $header)
    {
        return $this->setOpt(CURLOPT_HTTPHEADER, $header);
    }
    
    /**
     * 设置post请求参数信息
     * @param string|array $body
     * @return Curl
     */
    public function setBody($body)
    {
        return $this->setOpt(CURLOPT_POSTFIELDS, $body);
    }
    
    /**
     * 设置请求方式
     * @param string $method 请求方式: 'POST'、'GET' ...
     * @return Curl
     */
    public function setMethod(string $method)
    {
        return $this->setOpt(CURLOPT_CUSTOMREQUEST, $method);
    }
    
    /**
     * 设置curl post参数(通常用于表单提交设置)
     * explain: 这个POST是普通的 application/x-www-from-urlencoded 类型，多数被HTML表单使用
     * @param bool $is_post true: 提交一个post请求 false: default (默认为false)
     * @return Curl
     */
    public function setCurlPost(bool $is_post)
    {
        return $this->setOpt(CURLOPT_POST, $is_post ? 1 : 0);
    }
    
    /**
     * 设置是否返回请求头信息
     * @param bool $is_header_out
     * @return Curl
     */
    public function setRequestHeaderOut(bool $is_header_out)
    {
        return $this->setOpt(CURLINFO_HEADER_OUT, $is_header_out);
    }
    
    /**
     * 是否显示返回响应头信息
     * @param bool $is_show_header
     * @return Curl
     */
    public function setResponseHeaderOut(bool $is_show_header)
    {
        return $this->setOpt(CURLOPT_HEADER, $is_show_header);
    }
    
    /**
     * 设置超时时间,单位秒(s)
     * @param int $timeout
     * @return Curl
     */
    public function setTimeout(int $timeout)
    {
        return $this->setOpt(CURLOPT_TIMEOUT, $timeout);
    }
    
    /**
     * 设置执行请求完成后是否关闭curl资源
     * @param bool $exec_curl_close
     * @return $this
     */
    public function setExecCurlClose(bool $exec_curl_close)
    {
        $this->exec_curl_close = $exec_curl_close;
        return $this;
    }
    
    /**
     * 获取当前curl配置参数
     * @return array
     */
    public function getOptions(): array
    {
        return $this->options;
    }
    
    /**
     * @return CurlResponse
     */
    public function getResponse(): CurlResponse
    {
        return $this->response;
    }
    
    /**
     * @return mixed
     */
    public function getBody()
    {
        return $this->option(CURLOPT_POSTFIELDS);
    }
    
    /**
     * 获取当前curl资源操作符
     * @return resource
     */
    public function getCh(): resource
    {
        return $this->ch;
    }
    
    
    /**
     * 执行curl 请求
     * @param int $execOpts
     * @return CurlResponse
     */
    public function exec($execOpts = 0)
    {
        $execOpt = Criteria::opts($execOpts | $this->execOpts);
        $postFields = $this->getBody();
        $execOpt->bind(self::CURL_BODY_BODY_PARSER, function() use ($postFields, &$requestData) {
            if (!is_array($postFields)) {
                $parse_data = json_decode($postFields, true);
                if (!isset($parse_data) || !is_array($parse_data)) {
                    parse_str($postFields, $parse_data);
                }
                $this->setBody($parse_data);
            }
        })->bind(self::CURL_BODY_GET_PARSER, function () {
            if ($this->option(CURLOPT_CUSTOMREQUEST) === self::GET && $body = $this->getBody()) {
                $url = trim($this->url, '?');
                $this->setUrl(trim(trim($url, '&') . (strpos($url, '?') === false ? '?' : '&') . http_build_query($this->getBody()), '&'));
            }
        })->handle();
        $this->setOpt(CURLOPT_HEADERFUNCTION, function ($ch, $header) use (&$headers, &$httpData) {
            if ($content = trim($header)) {
                if ($httpData && strpos($content, ':') !== false) {
                    list($key, $value) = explode(':', $content,2);
                    $headers[$key] = trim($value);
                } else {
                    $httpData = explode(' ', $content, 3);
                }
            }
            return strlen($header);
        });
        $responseData = curl_exec($this->ch);
        $curlInfo = curl_getinfo($this->ch);
        $curl_err_no = curl_errno($this->ch);
        $curl_err_msg = curl_error($this->ch) ?: curl_strerror($curl_err_no);
        $response = new CurlResponse();
        $response->setProtocol(substr($httpData[0] ?? '', 5));
        $response->setStatusCode($httpData[1] ?? 0);
        $response->setReasonPhrase($httpData[2] ?? 'Failed');
        $response->setResponseData($responseData);
        $response->setResponseHeader($headers ?? []);
        $response->setCurlInfo(new CurlInfo($curlInfo));
        $response->setCurlErrorNo($curl_err_no);
        $response->setCurlErrorMsg($curl_err_msg);
        return $response;
    }
    
    /**
     * 执行一次性的请求
     * @param int $execOpts
     * @return CurlResponse
     */
    public function execOnce($execOpts = 0)
    {
        if (isset($this->response)) {
            return $this->response;
        } else {
            return $this->exec($execOpts);
        }
    }
    
    /**
     * @param int $execOpts
     * @return CurlResponse
     */
    public function execAndClose($execOpts = 0)
    {
        $response = $this->exec($execOpts);
        $this->close();
        return $response;
    }
    
    /**
     * 关闭并释放curl资源
     * @return $this
     */
    public function close()
    {
        is_resource($this->ch) && curl_close($this->ch);
        return $this;
    }
    
    
    
    /*---------------------------------------------- decrypt ----------------------------------------------*/
    /**
     * destruct
     */
    public function __destruct()
    {
        $this->close();
    }
    
}