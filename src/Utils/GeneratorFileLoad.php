<?php
/**
 * Author: 沧澜
 * Date: 2019-09-03
 */

namespace CalJect\Productivity\Utils;


use Closure;
use Generator;

class GeneratorFileLoad
{
    /**
     * 读取的文件路径
     * @var string
     */
    protected $dir = '';
    
    /**
     * GeneratorFileLoad constructor.
     * @param string $dir
     */
    public function __construct($dir)
    {
        $this->dir = realpath($dir);
    }
    
    /**
     * do handle
     * @param Closure $handle function($index, $value) {} when options = 1 or function($value) {}  when options = 0
     * @param int $options
     */
    public function eachFiles(Closure $handle, int $options = GeneratorLoad::OPT_NO_INDEX)
    {
        GeneratorLoad::each($this->readEachFiles(), $handle, $options);
    }
    
    /**
     * @return array
     */
    public function readToArray(): array
    {
        GeneratorLoad::each($this->readEachFiles(), function ($value) use (&$files) {
            $files[] = $value;
        });
        return $files ?? [];
    }
    
    /**
     * 读取目录下所有文件名
     * @return Generator
     */
    public function readEachFiles()
    {
        return $this->handleReadEachFiles($this->dir);
    }
    
    
    /**
     * 读取目录下所有文件名
     * @param string $dir 递归查找的路径名
     * @return Generator
     */
    private function handleReadEachFiles($dir)
    {
        /* ======== 转绝对路径 ======== */
        if ($handle = opendir($dir)) {
            while (($fl = readdir($handle)) !== false) {
                $temp = $dir . DIRECTORY_SEPARATOR . $fl;
                if (in_array($fl, ['.', '..']))
                    continue;
                is_dir($temp) ? yield $this->handleReadEachFiles($temp) : yield $temp;
            }
        }
    }
    
}