<?php
/**
 * Author: 沧澜
 * Date: 2019-09-04
 */

namespace CalJect\Productivity\Test\Component\Criteria;


use CalJect\Productivity\Components\Criteria\Branch\SwControl;
use CalJect\Productivity\Components\Criteria\Criteria;
use CalJect\Productivity\Exceptions\ClosureRunException;
use PHPUnit\Framework\TestCase;

class CriteriaTest extends TestCase
{
    /**
     * @return array
     */
    public function optsData()
    {
        return [
            'ck-opt-1' => [7, [1], []],
            'ck-opt-2' => [7, [1, 2], []],
            'ck-opt-3' => [7, [1, 2, 4], []],
            'ck-opt-4' => [7, [1], [8]],
            'ck-opt-5' => [7, [1, 2], [8]],
            'ck-opt-6' => [7, [1, 2, 4], [8]],
            'ck-opt-7' => [8, [8], [1, 2, 3, 4, 5, 6, 7]],
            'ck-opt-8' => [9, [1, 8], [2, 3, 4, 5, 6, 7, 16]],
        ];
    }
    
    /**
     * @dataProvider optsData
     * @param int $opts         配置值
     * @param array $validates  检查结果为true的值
     * @param array $noHases    检查结果为false的值
     * @param int $max          设置最大的绑定数字，默认1-10
     * @throws ClosureRunException
     */
    public function testOpts($opts, $validates, $noHases, $max = 10)
    {
        $cFunc = function ($key, $value = null) use (&$runArr) {
            return function () use (&$runArr, $key, $value) {
                $runArr[$key] = $value ?? $key;
            };
        };
        $criteria = Criteria::opts($opts);
        for ($i = 0; $i++ < $max;) {
            $criteria->bind($i, $cFunc($i));
        }
        $criteria->handle();
        foreach ($validates as $validate) {
            $this->assertArrayHasKey($validate, $runArr);
        }
        foreach ($noHases as $noHas) {
            $this->assertArrayNotHasKey($noHas, $runArr);
        }
    }
    
    /**
     * @return array
     */
    public function switchData()
    {
        return [
            'sw-1' => [200, 200],
            'sw-2' => ['0000', '0000'],
            'sw-3' => ['200', 200],
            'sw-4' => [0000, 'default'],
            'sw-5' => ['sss', 'default'],
            'sw-6' => ['test1', 'test1'],
            'sw-7' => ['test2', 'test2'],
            'sw-8' => ['test3', 'default'],
            'sw-9' => ['test4', 'test5'],
            'sw-10' => ['test5', 'test5'],
            'sw-11' => ['test6', 'default'],
            'sw-12' => ['test7', 'test5'],
            'sw-13' => ['test8', 'default'],
            'sw-14' => ['test9', 'test5'],
        ];
    }
    
    /**
     * @dataProvider switchData
     * @param string|int $data
     * @param mixed $result
     * @throws ClosureRunException
     */
    public function testSwitch($data, $result)
    {
        $criteria = Criteria::switch($data);
        $criteria->bind(200, function () {
            return 200;
        })->bind('0000', function () {
            return '0000';
        })->bind('test1', function () {
            return 'test1';
        })->bind('test2', function (SwControl $control) {
            return $control->getCheckValue();
        })->bind('test3', function (SwControl $control) {
            return call_user_func($control->getDefault(), $control);
        })->bind('test4', function (SwControl $control) {
            return call_user_func($control->getBinds()['test5'], $control);
        })->bind('test5', function () {
            return 'test5';
        })->bind('test6', function (SwControl $control) {
            return $control->callDefault();
        })->bind('test7', function (SwControl $control) {
            return $control->call('test5');
        })->default(function () {
            return 'default';
        })->bind('test8', SwControl::CLOSURE_DEFAULT())->bind('test9', SwControl::CLOSURE_BIND('test5'));;
        $this->assertSame($criteria->handle(), $result);
    }

    
}