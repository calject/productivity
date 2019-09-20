<?php
/**
 * Author: 沧澜
 * Date: 2019-09-04
 */

namespace CalJect\Productivity\Test\Component\Check;


use CalJect\Productivity\Components\Check\CkDef;
use PHPUnit\Framework\TestCase;

class CkDefTest extends TestCase
{
    /**
     * test CkDef method
     */
    public function testGetAndDefaultValue()
    {
        $ckDef = CkDef::make(['t1' => 't1-data', 'ttt' => 'ttt-data']);
        $this->assertEquals('t1-data', $ckDef['t1']);
        $this->assertEquals('ttt-data', $ckDef['ttt']);
        $this->assertSame(null, $ckDef['any-str']);
        $this->assertSame(null, $ckDef->get('any-str'));
        $this->assertSame(null, $ckDef->get('any-str', null));
        $this->assertNotSame(null, $ckDef->get('any-str', ''));
        $this->assertIsArray($ckDef());
        $this->assertIsArray($ckDef->toArray());
        $this->assertIsArray($ckDef->all());
        
        $ckDefStr = CkDef::make(['s1' => 'str1-data', 's2' => 'str2-data'], '');
        $this->assertSame('str1-data', $ckDefStr['s1']);
        $this->assertSame('str2-data', $ckDefStr['s2']);
        $this->assertSame('', $ckDefStr['s3']);
        $this->assertSame('', $ckDefStr['string']);
    }
}