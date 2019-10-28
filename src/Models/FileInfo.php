<?php
/**
 * Author: 沧澜
 * Date: 2019-10-28
 */

namespace CalJect\Productivity\Models;

use CalJect\Productivity\Components\DataProperty\CallDataProperty;

/**
 * Class FileInfo
 * @package CalJect\Productivity\Models
 *
 * ---------- set ----------
 * @method $this setFilePath(string $filePath)
 * @method $this setFileDir(string $fileDir)
 * @method $this setFileName(string $fileName)
 * @method $this setClass(string $class)
 * @method $this setNamespace(string $namespace)
 * @method $this setClassName(string $className)
 * @method $this setContent(string $content)
 * @method $this setCrown(string $crown)
 * @method $this setBelow(string $below)
 *
 * ---------- get ----------
 * @method string getFilePath()
 * @method string getFileDir()
 * @method string getFileName()
 * @method string getClass()
 * @method string getNamespace()
 * @method string getClassName()
 * @method string getContent()
 * @method string getCrown()
 * @method string getBelow()
 *
 */
class FileInfo extends CallDataProperty
{
    /**
     * 文件信息
     * @var string
     */
    protected $filePath, $fileDir, $fileName;
    
    /**
     * 类信息
     * @var string
     */
    protected $class, $namespace, $className;
    
    /**
     * 文件内容
     * @var string
     */
    protected $content, $crown, $below;
    
}