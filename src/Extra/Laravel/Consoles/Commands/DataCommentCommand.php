<?php
/**
 * Author: 沧澜
 * Date: 2019-10-30
 */

namespace CalJect\Productivity\Extra\Laravel\Consoles\Commands;


use CalJect\Productivity\Components\Comments\CallDataPropertyHeadComment as DataCommnet;
use CalJect\Productivity\Extra\Laravel\Contracts\Commands\Command;
use ReflectionException;

/**
 * Class DataCommentCommand
 * @package CalJect\Productivity\Extra\Laravel\Consoles\Commands
 */
class DataCommentCommand extends Command
{
    
    protected $signature = 'calject:comment:data
    {path : 执行目录或文件}
    {--def-cur : 生成当前类默认注释[--get/--set](未传入参数默认为该配置项)}
    {--def : 使用默认配置生成注释[--get/--set/--cur],使用--no-xxx取消}
    {--all : 应用所有配置[--get/--set/--pro/--apt],使用--no-xxx取消}
    {--get : 生成get方法注释}
    {--set : 生成set方法注释}
    {--pro : 生成property属性注释}
    {--apt : 生成adapter方法注释}
    {--cur : 仅生成当前class属性对应方法}
    {--no-get : 不生成get方法注释}
    {--no-set : 不生成set方法注释}
    {--no-pro : 不生成property属性注释}
    {--no-apt : 不生成adapter方法注释}
    {--no-cur : 生成所有属性,包含继承}';
    
    protected $description = '根据类属性生成类(get/set/apt/property)属性注释';
    
    /**
     * Execute the console command.
     * @return mixed
     * @throws ReflectionException
     */
    public function handle()
    {
        if (($path = $this->argument('path')) && (is_dir($path) || is_file($path))) {
            if ($this->option('all')) {
                $options = DataCommnet::COM_ALL;
            } elseif ($this->option('def')) {
                $options = DataCommnet::COM_GET | DataCommnet::COM_GET;
            } else {
                $options = DataCommnet::COM_GET | DataCommnet::COM_GET | DataCommnet::OPT_CREATE_CURRENT;
            }
            $this->option('get') && $options |= DataCommnet::COM_GET;
            $this->option('set') && $options |= DataCommnet::COM_SET;
            $this->option('pro') && $options |= DataCommnet::COM_PRO;
            $this->option('apt') && $options |= DataCommnet::COM_APT;
            $this->option('cur') && $options |= DataCommnet::OPT_CREATE_CURRENT;
            
            $this->option('no-get') && $options &= ~DataCommnet::COM_GET;
            $this->option('no-set') && $options &= ~DataCommnet::COM_SET;
            $this->option('no-pro') && $options &= ~DataCommnet::COM_PRO;
            $this->option('no-apt') && $options &= ~DataCommnet::COM_APT;
            $this->option('no-cur') && $options &= ~DataCommnet::OPT_CREATE_CURRENT;
            
            $dataComment = new DataCommnet();
            $dataComment->outputByCommand($this)
                ->options($options)
                ->handle($path);
        } else {
            $this->error("$path 不是一个合法的目录或者文件路径.");
        }
    }
}