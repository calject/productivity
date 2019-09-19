<?php
/**
 * Author: 沧澜
 * Date: 2019-09-04
 */

namespace CalJect\Productivity\Test\Component\Criteria;


use CalJect\Productivity\Components\Criteria\Criteria;
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
     * @param int $opts
     * @param array $validates
     * @param array $noHases
     * @param int $max
     */
    public function testOpts($opts, $validates, $noHases, $max = 10)
    {
        $runArr = [];
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

    
}