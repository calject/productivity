<?php
/**
 * Author: 沧澜
 * Date: 2019-10-25
 */

namespace CalJect\Productivity\Tests\Component\DataProperty\TestClass;

use CalJect\Productivity\Components\DataProperty\CallDataPropertyEmpty;

/**
 * Class TestCallDataPropertyCheckEmptyData
 * @package CalJect\Productivity\Tests\Component\DataProperty\TestClass
 * @method $this setVal1(string $val1)
 * @method $this setVal2(int $val1)
 * @method $this setVal3(string $val1)
 *
 * @method string getVal1()
 * @method int getVal2()
 * @method string getVal3()
 */
class TestCallDataPropertyCheckEmptyData extends CallDataPropertyEmpty
{
    
    /**
     * @note 数据1
     * @var string
     */
    protected $val1;
    
    /**
     * @note 数据2
     * @var int
     */
    protected $val2;
    
    /**
     * 数据3
     * @var string
     */
    protected $val3;
    
}