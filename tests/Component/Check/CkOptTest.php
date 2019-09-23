<?php
/**
 * Author: 沧澜
 * Date: 2019-09-04
 */

namespace CalJect\Productivity\Test\Component\Check;


use CalJect\Productivity\Components\Check\CkOpt;
use CalJect\Productivity\Exceptions\ClosureRunException;
use PHPUnit\Framework\TestCase;

class CkOptTest extends TestCase
{
    const MODEL_CHECK = 1;
    const MODEL_OR = 2;
    const MODEL_AND = 3;
    const MODEL_RUN = 4;
    
    /**
     * dataProvider
     * @return array
     */
    public function ckOptData()
    {
        return [
            'check-1'   => [3, 1,               true,   self::MODEL_CHECK],
            'check-2'   => [4, 1,               false,  self::MODEL_CHECK],
            'check-3'   => [4, 4,               true,   self::MODEL_CHECK],
            'or-1'      => [7, 1,               true,   self::MODEL_OR],
            'or-2'      => [7, 2,               true,   self::MODEL_OR],
            'or-3'      => [7, 4,               true,   self::MODEL_OR],
            'or-4'      => [7, 8,               false,  self::MODEL_OR],
            'or-5'      => [7, 9,               true,   self::MODEL_OR],
            'or-6'      => [7, [1],             true,   self::MODEL_OR],
            'or-7'      => [7, [1, 2],          true,   self::MODEL_OR],
            'or-8'      => [7, [1, 2, 4],       true,   self::MODEL_OR],
            'or-9'      => [7, [1, 2, 4, 8],    true,   self::MODEL_OR],
            'or-10'     => [7, [1, 2, 4, 9],    true,   self::MODEL_OR],
            'or-11'     => [7, [8, 9],          true,   self::MODEL_OR],
            'or-12'     => [7, [8, 24],         false,  self::MODEL_OR],
            'or-13'     => [7, [9],             true,   self::MODEL_OR],
            'and-1'     => [7, 1,               true,   self::MODEL_AND],
            'and-2'     => [7, 2,               true,   self::MODEL_AND],
            'and-3'     => [7, 4,               true,   self::MODEL_AND],
            'and-4'     => [7, 8,               false,  self::MODEL_AND],
            'and-5'     => [7, 9,               false,  self::MODEL_AND],
            'and-6'     => [7, [1],             true,   self::MODEL_AND],
            'and-7'     => [7, [1, 2],          true,   self::MODEL_AND],
            'and-8'     => [7, [1, 2, 4],       true,   self::MODEL_AND],
            'and-9'     => [7, [1, 2, 4, 8],    false,  self::MODEL_AND],
            'and-10'    => [7, [1, 2, 4, 9],    false,  self::MODEL_AND],
            'and-11'    => [7, [9],             false,  self::MODEL_AND],
            'run-1'     => [7, ['check' => 1, 'match' => function() {
                return 'match-run-1';
            }], 'match-run-1', self::MODEL_RUN],
            'run-2'     => [7, ['check' => 8, 'match' => function() {
                return 'match-run-2';
            }], false, self::MODEL_RUN],
            'run-3'     => [7, ['check' => 8, 'match' => function() {
                return 'match-run-3';
            }, 'mis_match' => function() {
                return 'mis-match-run-3';
            }], 'mis-match-run-3', self::MODEL_RUN],
            'run-4'     => [7, ['check' => 2, 'mis_match' => function() {
                return 'mis-match-run-4';
            }], true, self::MODEL_RUN],
        ];
    }
    
    /**
     * @dataProvider ckOptData
     * @param int $opts
     * @param int $check
     * @param bool $determine
     * @param string $model
     * @throws ClosureRunException
     */
    public function testOpts($opts, $check, $determine, $model)
    {
        $ckOpt = CkOpt::make($opts);
        if ($model === self::MODEL_CHECK) {
            $this->assertSame($determine, $ckOpt->check($check));
        } elseif ($model === self::MODEL_OR) {
            $this->assertSame($determine, $ckOpt->checkOr($check));
        } elseif ($model === self::MODEL_AND) {
            $this->assertSame($determine, $ckOpt->checkAnd($check));
        } elseif ($model == self::MODEL_RUN) {
            $this->assertSame($determine, $ckOpt->checkRun($check['check'], $check['match'] ?? null, $check['mis_match'] ?? null));
        }
    }
    
}