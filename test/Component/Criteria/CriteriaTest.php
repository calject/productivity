<?php
/**
 * Author: 沧澜
 * Date: 2019-09-04
 */

namespace CalJect\Productivity\Test\Component\Criteria;


use CalJect\Productivity\Component\Criteria\Criteria;
use PHPUnit\Framework\TestCase;

class CriteriaTest extends TestCase
{
    /**
     * @return array
     */
    public function optsData()
    {
        return [
            'ck1' => [7, [1], []],
            'ck2' => [7, [1, 2], []],
            'ck3' => [7, [1, 2, 4], []],
            'ck4' => [7, [1], [8]],
            'ck5' => [7, [1, 2], [8]],
            'ck6' => [7, [1, 2, 4], [8]],
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