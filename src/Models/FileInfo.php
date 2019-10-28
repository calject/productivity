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
 * ---------- set ----------
 * @method $this setFilePath(string $filePath)
 * @method $this setFileDir($fileDir)
 * @method $this setFileName($fileName)
 * @method $this setClass(string $class)
 * @method $this setNamespace($namespace)
 * @method $this setClassName($className)
 * @method $this setContent(string $content)
 * @method $this setCrown($crown)
 * @method $this setBelow($below)

 * ---------- get ----------
 * @method string getFilePath()
 * @method mixed  getFileDir()
 * @method mixed  getFileName()
 * @method string getClass()
 * @method mixed  getNamespace()
 * @method mixed  getClassName()
 * @method string getContent()
 * @method mixed  getCrown()
 * @method mixed  getBelow()
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