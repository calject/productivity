<?php
/**
 * Author: 沧澜
 * Date: 2019-09-03
 */

namespace CalJect\Productivity\Utils;


class FileUtil
{
    /**
     * 读取目录下所有文件名
     * @param string $dir       读取的路径
     * @param bool $recursion   是否递归查找所有子目录 true: 查找所有子目录 false:仅查找指定目录，不遍历子目录
     * @return array
     */
    final public static function readAllFilesInDir(string $dir, $recursion = true)
    {
        $files = [];
        $dir = realpath($dir);
        if(!is_dir($dir)) return [];
        if($handle = opendir($dir)) {
            while (($fl = readdir($handle)) !== false) {
                $temp = $dir . DIRECTORY_SEPARATOR . $fl;
                //如果不加 !in_array($fl, ['.', '..'] 则会造成把$dir的父级目录也读取出来
                if ($recursion && is_dir($temp) && !in_array($fl, ['.', '..'])) {
                    /* ======== 目录 ======== */
                    $files = array_merge_recursive($files, self::readAllFilesInDir($temp));
                } else {
                    if (!in_array($fl, ['.', '..']) && !is_dir($temp)) {
                        /* ======== 文件 ======== */
                        $files[] = $temp;
                    }
                }
            }
        }
        return $files;
    }
}