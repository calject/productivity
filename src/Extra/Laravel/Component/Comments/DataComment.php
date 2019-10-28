<?php
/**
 * Author: 沧澜
 * Date: 2019-10-28
 */

namespace CalJect\Productivity\Extra\Laravel\Component\Comments;

use CalJect\Productivity\Utils\GeneratorFileLoad;

/**
 * Class DataComment
 * @package CalJect\Productivity\Extra\Laravel\Component\Comments
 */
class DataComment
{
    
    /**
     * @param string $path
     */
    public function handle(string $path)
    {
        if (is_dir($path)) {
            (new GeneratorFileLoad($path))->eachFiles(function ($file) {
                static $n = 0;
            });
        } else {
            echo '必须是一个合法的目录.';
        }
    }
}