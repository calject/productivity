<?php
/**
 * Author: 沧澜
 * Date: 2019-10-28
 */

namespace CalJect\Productivity\Contracts\Comments;


use CalJect\Productivity\Contracts\Listener\ClassCommentListenerInterface;
use CalJect\Productivity\Models\FileInfo;
use CalJect\Productivity\Utils\GeneratorFileLoad;
use CalJect\Productivity\Utils\GeneratorLoad;
use ReflectionClass;
use ReflectionException;

abstract class ClassComment
{
    /**
     * 执行错误log
     * @var array
     */
    protected $errLogs = [];
    
    /**
     * 监听者列表
     * @var ClassCommentListenerInterface[]
     */
    protected $listeners = [];
    
    /**
     * @param FileInfo $fileInfo
     * @param ReflectionClass $refClass
     * @param string $filePath
     * @return mixed
     */
    abstract protected function create(FileInfo $fileInfo, ReflectionClass $refClass, string $filePath);
    
    
    /**
     * @param string $path
     * @throws ReflectionException
     */
    public function handle(string $path)
    {
        if (!is_dir($path)) {
            if (is_file($path)) {
                $this->doHandle($path);
            } else {
                echo "必须为一个正确的目录或者文件.";
                return;
            }
        } else {
            (new GeneratorFileLoad($path))->eachFiles(function ($index, $filePath) {
                $this->doHandle($filePath);
                $this->notify($index, $filePath);
            }, GeneratorLoad::OPT_GET_INDEX);
        }
    }
    
    /**
     * @param $filePath
     * @return mixed
     * @throws ReflectionException
     */
    private function doHandle($filePath)
    {
        if ($fileInfo = $this->getFileInfo($filePath)) {
            $class = $fileInfo->getClass();
            $refClass = new ReflectionClass($class);
            if ($refClass->isAbstract() || $refClass->isInterface()) {
                $this->errLog("class(${class}) can not be abstract or interface.");
            }
            /* ======== 创建注释 ======== */
            $content = $this->create($fileInfo, $refClass, $filePath);
            /* ======== 写入文件 ======== */
            file_put_contents($filePath, $content);
        } else {
            $this->errLog("$filePath 路径类解析异常.");
        }
    }
    
    
    /*---------------------------------------------- function ----------------------------------------------*/
    /**
     * 获取文件信息(类及数据切分)
     * @param $filePath
     * @return FileInfo|false
     */
    protected function getFileInfo(string $filePath)
    {
        $content = file_get_contents($filePath);
        $fileName = basename($filePath);
        $className = rtrim($fileName, '.php');
        
        /* ======== 查询命名空间 ======== */
        preg_match("#(?:namespace)(.*);#", $content, $namespace_arr);
        
        /* ======== 查询类分割class及上部分 ======== */
        preg_match("#(.*?(?=(?:class|abstract|interface) .*))(.*)#s", $content, $explodeArr);
        
        $namespace = trim($namespace_arr[1] ?? '');
        $class = $namespace.'\\'.$className;
        if (!class_exists($class)) {
            return false;   // 指定类不存在,返回false
        }
        return (new FileInfo())->setInArray([
            'filePath' => $filePath,
            'fileDir' => dirname($filePath),
            'fileName' => $fileName,
            'class' => $class,
            'namespace' => $namespace,
            'className' => $className,
            'content' => $content,
            'crown' => $explodeArr[1],
            'below' => $explodeArr[2]
        ]);
    }
    
    /**
     * @param string $errMsg
     * @return array
     */
    protected function errLog($errMsg)
    {
        return $this->errLogs[] = [
            'errMsg' => $errMsg
        ];
    }
    
    protected function notify($index, $fileInfo)
    {
        foreach ($this->listeners as $listener) {
            $listener->listen($index, $fileInfo);
        }
    }
    
    /**
     * @return array
     */
    public function getErrLogs(): array
    {
        return $this->errLogs;
    }
}