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
 * Class CallDataPropertyDisable
 * @package CalJect\Productivity\Components\DataProperty
 */
abstract class CallDataPropertyDisable extends CallDataProperty
{
    /**
     * check tag
     * @var string
     */
    protected $checkTag = 'noCheck';
    
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
                return;
            } else {
                $this->throwVerifyException(static::class, $name, $docComment);
            }
        }
    }
}