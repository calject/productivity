<?php
/**
 * Author: 沧澜
 * Date: 2019-10-25
 */

namespace CalJect\Productivity\Extra\Laravel\Consoles\Commands;


use CalJect\Productivity\Components\Check\CkDef;
use CalJect\Productivity\Extra\Laravel\Component\Comments\ModelComment;
use Illuminate\Console\Command;

class ModelCommentCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'calject:comment:model {dir?}';
    
    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '根据模型生成表注释; 参数: --dir=xxx:指定模型路径(不传入默认为模型根目录)';
    
    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }
    
    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $options = CkDef::make($this->argument());
        if ($dir = $options['dir']) {
            if (!is_dir($dir)) {
                echo "${dir}不是一个正确的目录";
                return;
            }
        }else {
            /* ======== 默认为model根目录下 ======== */
            $dir = app_path().'/Models';
        }
        $modelComments = new ModelComment();
        $errors = $modelComments->handle($dir);
        if (empty($result)) {
            echo "all success";
        }else {
            $buffer = '';
            foreach ($errors as $error) {
                $buffer .= "error${error['index']}:".$error['err_msg']."\n";
            }
            echo $buffer;
        }
    }
}