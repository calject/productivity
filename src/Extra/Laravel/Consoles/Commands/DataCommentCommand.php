<?php
/**
 * Author: 沧澜
 * Date: 2019-10-30
 */

namespace CalJect\Productivity\Extra\Laravel\Consoles\Commands;


use CalJect\Productivity\Components\Comments\CallDataPropertyHeadComment;
use CalJect\Productivity\Extra\Laravel\Contracts\Commands\Command;
use ReflectionException;

/**
 * Class DataCommentCommand
 * @package CalJect\Productivity\Extra\Laravel\Consoles\Commands
 */
class DataCommentCommand extends Command
{
    
    protected $signature = 'calject:comment:data {path : 执行目录或文件}';
    
    protected $description = '根据类属性生成类(get/set/apt/property)属性注释';
    
    /**
     * Execute the console command.
     * @return mixed
     * @throws ReflectionException
     */
    public function handle()
    {
        if (($path = $this->argument('path')) && (is_dir($path) || is_file($path))) {
            $dataComment = new CallDataPropertyHeadComment();
            $dataComment->outputByCommand($this)->handle($path);
        } else {
            $this->error("$path 不是一个合法的目录或者文件路径.");
        }
    }
}