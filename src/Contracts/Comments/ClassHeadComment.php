<?php
/**
 * Author: 沧澜
 * Date: 2019-10-28
 */

namespace CalJect\Productivity\Contracts\Comments;

use CalJect\Productivity\Models\FileInfo;
use ReflectionClass;

/**
 * Class ClassHeadComment
 * @package CalJect\Productivity\Contracts\Comments
 */
abstract class ClassHeadComment extends ClassComment
{
    
    /**
     * @param FileInfo $fileInfo
     * @param ReflectionClass $refClass
     * @param string $filePath
     * @return string
     */
    abstract protected function getComments(FileInfo $fileInfo, ReflectionClass $refClass, string $filePath): string;
    
    /**
     * @param FileInfo $fileInfo
     * @param ReflectionClass $refClass
     * @param string $filePath
     * @return mixed
     */
    protected function create(FileInfo $fileInfo, ReflectionClass $refClass, string $filePath)
    {
        $crown = $fileInfo->getCrown();
        preg_match_all("#(?:<\\?php(?:\\n)+)?/[*]{2}(.*?)\\*/\\n#s", $crown, $crown_no_com);
        if ($matches = $crown_no_com[0]) {
            $comment_match = $matches[count($matches) - 1];
            if (strpos($comment_match, '<?php') === false) {
                $crown = str_replace($comment_match, '', $crown);
            }
        }
        return $crown . $this->getComments($fileInfo, $refClass, $filePath) . $fileInfo->getBelow();
    }
    
    /*---------------------------------------------- function ----------------------------------------------*/
    
    /**
     * @param FileInfo $fileInfo
     * @param string $comment
     * @return string
     */
    protected function commentWithClassHead(FileInfo $fileInfo, string $comment = '')
    {
        $head = "/**\n * Class " . $fileInfo->getClassName() . "\n * @package " . $fileInfo->getNamespace() . "\n";
        return $head . $comment . " */\n";
    }
    
}