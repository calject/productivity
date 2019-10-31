<?php
/**
 * Author: 沧澜
 * Date: 2019-10-31
 */

namespace CalJect\Productivity\Components\DataProperty;

use CalJect\Productivity\Components\DataProperty\Exception\VerifyException;
use CalJect\Productivity\Utils\CommentUtil;
use ReflectionException;
use ReflectionProperty;

/**
 * Class CallDataPropertyOnCheck
 * @package CalJect\Productivity\Components\DataProperty
 */
abstract class CallDataPropertyOnCheck extends CallDataProperty
{
    
    /**
     * check tag
     * @var string
     */
    protected $checkTag = 'check';
    
    /**
     * @param string $name
     * @param $value
     * @return void
     * @throws ReflectionException
     * @throws VerifyException
     */
    public function _callGet(string $name, $value)
    {
        if (!$value) {
            $refPro = new ReflectionProperty($this, $name);
            $docComment = $refPro->getDocComment();
            if (CommentUtil::checkCommentTag($this->checkTag, $docComment)) {
                $this->throwVerifyException(static::class, $name, $docComment);
            }
        }
    }
}