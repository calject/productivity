<?php
/**
 * Author: 沧澜
 * Date: 2019-10-30
 */

namespace CalJect\Productivity\Contracts\Listener;

/**
 * Interface ClassCommentListener
 * @package CalJect\Productivity\Contracts\Listener
 */
interface ClassCommentListenerInterface
{
    /**
     * @param int $index
     * @param string $filePath
     * @return mixed
     */
    public function listen(int $index, $filePath);
}