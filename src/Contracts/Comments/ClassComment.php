<?php
/**
 * Author: 沧澜
 * Date: 2019-10-28
 */

namespace CalJect\Productivity\Contracts\Comments;


use CalJect\Productivity\Contracts\Listener\ClassCommentListenerInterface;
use CalJect\Productivity\Extra\Laravel\Contracts\Commands\Command;
use CalJect\Productivity\Models\FileInfo;
use CalJect\Productivity\Utils\GeneratorFileLoad;
use CalJect\Productivity\Utils\GeneratorLoad;
use Closure;
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
     * 输出回调
     * @var Closure function(string $type = 'info', string $message)
     * @explain type: info 、 error 、 success 、 finish
     */
    protected $output = null;
    
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
                $this->errLog("$path 必须为一个正确的目录或者文件..");
            }
        } else {
            (new GeneratorFileLoad($path))->eachFiles(function ($index, $filePath) {
                $this->doHandle($filePath);
                $this->notify($index, $filePath);
            }, GeneratorLoad::OPT_GET_INDEX);
            $this->runOutput('finish', 'finish');
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
            $this->runOutput('success', "output ==> $filePath");
            /* ======== 写入文件 ======== */
            file_put_contents($filePath, $content);
        } else {
            $this->errLog("$filePath 路径类解析异常.");
        }
    }
    
    /**
     * @return array
     */
    public function getErrLogs(): array
    {
        return $this->errLogs;
    }
    
    /**
     * @param Closure $output
     * @return $this
     */
    public function output(Closure $output)
    {
        $this->output = $output;
        return $this;
    }
    
    /**
     * @param Command $command
     * @return $this
     */
    public function outputByCommand(Command $command)
    {
        $this->output = function ($type, $message) use ($command) {
            $message = "[$type] $message";
            if ($type == 'error') {
                $command->error($message);
            } elseif (in_array($type, ['success', 'info', 'finish'])) {
                $command->info($message);
            } else {
                $command->line($message);
            }
        };
        return $this;
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
        $this->runOutput('error', $errMsg);
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
     * @param string $type
     * @param string $message
     */
    protected function runOutput(string $type, string $message)
    {
        $this->output && call_user_func_array($this->output, [$type, $message]);
    }
}