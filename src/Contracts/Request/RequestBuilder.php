<?php
/**
 * Author: 沧澜
 * Date: 2019-09-20
 */

namespace CalJect\Productivity\Contracts\Request;

use CalJect\Productivity\Components\Responses\InteriorResponse;

interface RequestBuilder
{
    /**
     * 请求接口
     * @param mixed $options  配置参数
     * @return mixed
     */
    public function url($options);
    
    /**
     * 请求头信息
     * @param mixed $options
     * @param mixed $body   body数据信息
     * @return array
     */
    public function header($options, $body = []): array;
    
    /**
     * 请求参数
     * @param mixed $options    配置参数
     * @param array $params     请求主体参数
     * @return array|mixed
     */
    public function body($options, array $params);
    
    /**
     * 请求返回数据解析并返回结果
     * @param mixed $options    请求参数配置
     * @param string $result    返回的数据对象
     * @return InteriorResponse 结果对象
     */
    public function parse($options, string $result): InteriorResponse;
    
    /**
     * 原始请求数据(未加密)
     * @return array
     */
    public function rawParams(): array;
    
    /**
     * 获取解密内容数组
     * @return array
     */
    public function decryParams(): array;
    
    /**
     * (获取/生成[订单不存在时])唯一订单号
     * @return string
     */
    public function orderNo(): string;
    
    /**
     * (获取/生成[流水号不存在])唯一订单号交易流水号
     * @return string
     */
    public function serialNo(): string;
    
}