<?php
/**
 * Author: 沧澜
 * Date: 2019-09-23
 */

namespace CalJect\Productivity\Tests\Component\Result\TestClass;

use CalJect\Productivity\Contracts\ToArray;

class TestBResponseData implements ToArray
{
    /**
     * @var mixed
     */
    protected $code;
    /**
     * @var string
     */
    protected $msg;
    
    /**
     * TestAResponseData constructor.
     * @param mixed $code
     * @param string $msg
     */
    public function __construct($code, string $msg)
    {
        $this->code = $code;
        $this->msg = $msg;
    }
    
    /**
     * @param array $data
     * @return TestBResponseData
     */
    public static function makeByArray(array $data)
    {
        return new static($data['code'], $data['msg']);
    }
    
    /**
     * @return array
     */
    public function toArray(): array
    {
        return [
            'code' => $this->code,
            'msg' => $this->msg
        ];
    }
    
    /**
     * @return string
     */
    public function __toString()
    {
        return "{$this->code}-{$this->msg}";
    }
}