<?php
/**
 * Author: 沧澜
 * Date: 2019-09-16
 */

namespace CalJect\Productivity\Component\Http\Curl;

use CalJect\Productivity\Contracts\ArrayAccess\AbsArrayAccessWithArray;

/**
 * Class CurlInfo
 * @package CalJect\Productivity\Component\Http\Curl
 */
class CurlInfo extends AbsArrayAccessWithArray
{
    /*
    |--------------------------------------------------------------------------
    | curl_info 参数列表
    |--------------------------------------------------------------------------
    | curl_info 参数列表
    |
    */
    protected $url;
    protected $content_type;
    protected $http_code;
    protected $header_size;
    protected $request_size;
    protected $filetime;
    protected $ssl_verify_result;
    protected $redirect_count;
    protected $total_time;
    protected $namelookup_time;
    protected $connect_time;
    protected $pretransfer_time;
    protected $size_upload;
    protected $size_download;
    protected $speed_download;
    protected $speed_upload;
    protected $download_content_length;
    protected $upload_content_length;
    protected $starttransfer_time;
    protected $redirect_time;
    protected $redirect_url;
    protected $primary_ip;
    protected $certinfo;
    protected $primary_port;
    protected $local_ip;
    protected $local_port;
    protected $request_header;
    
    /**
     * __set异常设置数组
     * @var array
     */
    protected $err_set = [];
    
    /**
     * curl info 数组
     * @var array
     */
    protected $curl_info = [];
    
    public function __construct(array $curl_info)
    {
        $this->curl_info = $curl_info;
        $this->setArrayAccessData($curl_info);
        $this->init($curl_info);
    }
    
    /**
     * 初始化
     * @param array $curl_info
     */
    protected function init(array $curl_info)
    {
        foreach ($curl_info as $key => $value) {
            $this->$key = $value;
        }
    }
    
    /**
     * @param string $name
     * @param mixed $value
     */
    public function __set($name, $value)
    {
        // 设置了不存在的属性，存放到异常设置数组内
        $this->err_set[$name] = $value;
    }
    
    /*---------------------------------------------- get ----------------------------------------------*/
    
    /**
     * @return mixed
     */
    public function getUrl()
    {
        return $this->url;
    }
    
    /**
     * @return mixed
     */
    public function getContentType()
    {
        return $this->content_type;
    }
    
    /**
     * @return mixed
     */
    public function getHttpCode()
    {
        return $this->http_code;
    }
    
    /**
     * @return mixed
     */
    public function getHeaderSize()
    {
        return $this->header_size;
    }
    
    /**
     * @return mixed
     */
    public function getRequestSize()
    {
        return $this->request_size;
    }
    
    /**
     * @return mixed
     */
    public function getFiletime()
    {
        return $this->filetime;
    }
    
    /**
     * @return mixed
     */
    public function getSslVerifyResult()
    {
        return $this->ssl_verify_result;
    }
    
    /**
     * @return mixed
     */
    public function getRedirectCount()
    {
        return $this->redirect_count;
    }
    
    /**
     * @return mixed
     */
    public function getTotalTime()
    {
        return $this->total_time;
    }
    
    /**
     * @return mixed
     */
    public function getNamelookupTime()
    {
        return $this->namelookup_time;
    }
    
    /**
     * @return mixed
     */
    public function getConnectTime()
    {
        return $this->connect_time;
    }
    
    /**
     * @return mixed
     */
    public function getPretransferTime()
    {
        return $this->pretransfer_time;
    }
    
    /**
     * @return mixed
     */
    public function getSizeUpload()
    {
        return $this->size_upload;
    }
    
    /**
     * @return mixed
     */
    public function getSizeDownload()
    {
        return $this->size_download;
    }
    
    /**
     * @return mixed
     */
    public function getSpeedUpload()
    {
        return $this->speed_upload;
    }
    
    /**
     * @return mixed
     */
    public function getSpeedDownload()
    {
        return $this->speed_download;
    }
    
    /**
     * @return mixed
     */
    public function getDownloadContentLength()
    {
        return $this->download_content_length;
    }
    
    /**
     * @return mixed
     */
    public function getUploadContentLength()
    {
        return $this->upload_content_length;
    }
    
    /**
     * @return mixed
     */
    public function getStarttransferTime()
    {
        return $this->starttransfer_time;
    }
    
    /**
     * @return mixed
     */
    public function getRedirectTime()
    {
        return $this->redirect_time;
    }
    
    /**
     * @return mixed
     */
    public function getRedirectUrl()
    {
        return $this->redirect_url;
    }
    
    /**
     * @return mixed
     */
    public function getPrimaryIp()
    {
        return $this->primary_ip;
    }
    
    /**
     * @return mixed
     */
    public function getCertinfo()
    {
        return $this->certinfo;
    }
    
    /**
     * @return mixed
     */
    public function getPrimaryPort()
    {
        return $this->primary_port;
    }
    
    /**
     * @return mixed
     */
    public function getLocalIp()
    {
        return $this->local_ip;
    }
    
    /**
     * @return mixed
     */
    public function getLocalPort()
    {
        return $this->local_port;
    }
    
    /**
     * @return mixed
     */
    public function getRequestHeader()
    {
        return $this->request_header;
    }
    
    /**
     * @return array
     */
    public function getErrSet(): array
    {
        return $this->err_set;
    }
    
    /**
     * 获取当前curl_info 数组
     * @return array
     */
    public function getCurlInfo()
    {
        return $this->curl_info;
    }
    
}