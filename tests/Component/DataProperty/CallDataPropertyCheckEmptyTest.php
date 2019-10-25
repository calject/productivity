<?php
/**
 * Author: 沧澜
 * Date: 2019-10-25
 */

namespace CalJect\Productivity\Tests\Component\DataProperty;


use CalJect\Productivity\Components\DataProperty\Exception\VerifyException;
use CalJect\Productivity\Tests\Component\DataProperty\TestClass\TestCallDataPropertyCheckEmptyData;
use PHPUnit\Framework\TestCase;

class CallDataPropertyCheckEmptyTest extends TestCase
{
    
    /**
     * @dataProvider
     */
    public function testDataCall()
    {
        $this->expectException(VerifyException::class);
        $this->expectExceptionMessage('TestCallDataPropertyCheckEmptyData::val3 不能为空');
        $data = new TestCallDataPropertyCheckEmptyData();
        $data->getVal3();
    }
    
    /**
     * @dataProvider
     */
    public function testDataCallWithNote()
    {
        $this->expectException(VerifyException::class);
        $this->expectExceptionMessage('数据1[val1] 字段不能为空');
        $data = new TestCallDataPropertyCheckEmptyData();
        $data->getVal1();
    }
    
    
    
}