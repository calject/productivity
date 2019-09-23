<?php
/**
 * Author: 沧澜
 * Date: 2019-09-23
 */

namespace CalJect\Productivity\Tests\Component\Result;


use CalJect\Productivity\Components\Responses\InteriorResponse;
use CalJect\Productivity\Components\Result\Result;
use CalJect\Productivity\Exceptions\ClosureRunException;
use CalJect\Productivity\Exceptions\TypeCheckException;
use CalJect\Productivity\Tests\Component\Result\TestClass\TestAResponse;
use CalJect\Productivity\Tests\Component\Result\TestClass\TestAResponseData;
use CalJect\Productivity\Tests\Component\Result\TestClass\TestBResponse;
use CalJect\Productivity\Tests\Component\Result\TestClass\TestBResponseData;
use CalJect\Productivity\Tests\Component\Result\TestClass\TestCResponse;
use CalJect\Productivity\Tests\Component\Result\TestClass\TestDResponse;
use PHPUnit\Framework\TestCase;

class ResultTest extends TestCase
{
    
    
    public function resultData()
    {
        $successDataA = ['code' => '200', 'msg' => 'successA'];
        $successDataB = ['code' => '0000', 'msg' => 'successB'];
        $errorDataA = ['code' => '422', 'msg' => 'errorA'];
        $errorDataB = ['code' => 'TA400021', 'msg' => 'errorB'];
        
        $responseSuccessA = InteriorResponse::success('success', $successDataA);
        $responseSuccessB = InteriorResponse::success('success', $successDataB);
        $responseErrorA = InteriorResponse::error(422, 'success', $errorDataA);
        $responseErrorB = InteriorResponse::error(500, 'success', $errorDataB);
        
        $convert = function (array $data) {
            return "{$data['code']}-{$data['msg']}";
        };
        
        $convertDataA = function (array $data) {
            return new TestAResponseData($data['code'], $data['msg']);
        };
        
        $convertDataB = function (array $data) {
            return new TestBResponseData($data['code'], $data['msg']);
        };
        
        return [
            /* ======== success ======== */
            'success1' => [
                'response' => $responseSuccessA,
                'resultClass' => TestAResponse::class,
                'options' => Result::OPT_CONVERT_IN_ALL,
                'convert' => $convert,
                'validate' => '200-successA'
            ],
            'success2' => [
                'response' => $responseSuccessB,
                'resultClass' => TestBResponse::class,
                'options' => Result::OPT_CONVERT_IN_SUCCESS,
                'convert' => $convert,
                'validate' => '0000-successB'
            ],
            'success3' => [
                'response' => $responseSuccessB,
                'resultClass' => TestBResponse::class,
                'options' => Result::OPT_CONVERT_IN_ERROR,
                'convert' => $convert,
                'validate' => null
            ],
            'success4' => [
                'response' => $responseSuccessB,
                'resultClass' => TestBResponse::class,
                'options' => Result::OPT_CONVERT_IN_ALL,
                'convert' => null,
                'validate' => null
            ],
            'success5' => [
                'response' => $responseSuccessA,
                'resultClass' => TestBResponse::class,
                'options' => Result::OPT_CONVERT_IN_ALL,
                'convert' => $convertDataA,
                'validate' => TestAResponseData::makeByArray($successDataA)
            ],
            /* ======== error ======== */
            'err1' => [
                'response' => $responseErrorA,
                'resultClass' => TestCResponse::class,
                'options' => Result::OPT_CONVERT_IN_ALL,
                'convert' => $convert,
                'validate' => '422-errorA'
            ],
            'err2' => [
                'response' => $responseErrorB,
                'resultClass' => TestDResponse::class,
                'options' => Result::OPT_CONVERT_IN_ERROR,
                'convert' => $convert,
                'validate' => 'TA400021-errorB'
            ],
            'err3' => [
                'response' => $responseErrorB,
                'resultClass' => TestAResponse::class,
                'options' => Result::OPT_CONVERT_IN_ALL,
                'convert' => $convertDataA,
                'validate' => TestAResponseData::class
            ],
            'err4' => [
                'response' => $responseErrorB,
                'resultClass' => TestAResponse::class,
                'options' => Result::OPT_CONVERT_IN_ALL,
                'convert' => $convertDataB,
                'validate' => TestBResponseData::class
            ],
            'err5' => [
                'response' => $responseErrorB,
                'resultClass' => TestDResponse::class,
                'options' => Result::OPT_CONVERT_IN_SUCCESS,
                'convert' => $convertDataA,
                'validate' => null
            ],
        ];
    }
    
    /**
     * @dataProvider resultData
     * @param InteriorResponse $response
     * @param string $resultClass
     * @param $options
     * @param $convert
     * @param $validate
     * @throws ClosureRunException
     * @throws TypeCheckException
     */
    public function testResult(InteriorResponse $response, $resultClass, $options, $convert, $validate)
    {
        $result = new Result($response);
        $resultClass && $result->resultClass($resultClass);
        $options && $result->options($options);
        $convert && $result->convert($convert);
        $responseObject = $result->exec();
        
        if ($resultClass) {
            $this->assertInstanceOf($resultClass, $responseObject);
            $this->assertTrue(get_class($responseObject) == $resultClass);
        }
        if ($convert) {
            if (is_object($validate)) {
                $this->assertSame($validate->__toString(), $responseObject->getResponseObject()->__toString());
            } elseif (class_exists($validate)){
                $this->assertInstanceOf($validate, $responseObject->getResponseObject());
            } else {
                $this->assertSame($validate, $responseObject->getResponseObject());
            }
        } else {
            $this->assertSame(null, $responseObject->getResponseObject());
        }
    }
    
    /**
     *
     * @expectedException CalJect\Productivity\Exceptions\TypeCheckException
     * @throws ClosureRunException
     * @throws TypeCheckException
     */
    public function testResultTypeCheckException()
    {
        $result = new Result(InteriorResponse::success());
        $result->resultClass('assss');
        $result->exec();
    }
    
}