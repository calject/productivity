<?php
/**
 * Author: 沧澜
 * Date: 2019-10-25
 */

namespace CalJect\Productivity\Extra\Laravel\Component\Comments;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use ReflectionClass;

/**
 * Class ModelComments
 * @package CalJect\Productivity\Extra\Laravel\Component\Comments
 */
class ModelComment
{
    /**
     * 查询表信息
     */
    const SQL_QUERY_COLUMN = <<<COLUMN
SELECT
	column_name AS 'column_name',
	data_type   AS 'data_type',
	character_maximum_length  AS 'character_maximum_length',
	numeric_precision AS 'numeric_precision',
	numeric_scale AS 'numeric_scale',
	is_nullable AS 'is_nullable',
	CASE
		WHEN extra = 'auto_increment' THEN 1
		ELSE 0
    END AS 'is_auto_increment',
	column_default AS 'default',
	column_comment AS 'comment'
FROM
	Information_schema.columns
WHERE
	table_Name = '%s' and table_schema = '%s';
COLUMN;
    
    /**
     * 查询表创建信息
     */
    const SQL_QUERY_TABLE = "SELECT table_name FROM information_schema.TABLES WHERE table_name = '%s' and table_schema = '%s';";
    
    
    const COLUMN_TYPE_STR_POS = 'strpos';
    const COLUMN_TYPE_MATCH = 'match';
    
    // 默认保存数据类型
    protected $columnMatchDefault = 'string';
    
    // 最长的类型字符串，仅用于对齐
    protected $columnMaxString = "string";
    
    // 列值数据匹配
    protected $columnMatch = [
        'int' => [
            'type' => 'strpos',
            'match' => 'int'
        ],
        'string' => [
            'type' => 'match',
            'match' => '#^(int|float)#'
        ]
    ];
    
    /**
     * 处理
     * @param string $dir
     * @return array
     */
    public function handle(string $dir)
    {
        $files = $this->readAllFilesInDir($dir);
        $err_log = [];
        foreach ($files as $index => $file_path) {
            if ($fileInfo = $this->getFileInfo($file_path)) {
                $class = $fileInfo['info']['class'];
                $refClass = new ReflectionClass($class);
                if ($refClass->isAbstract() || $refClass->isInterface() || !is_subclass_of($class, Model::class)) {
                    $this->errLog($index, "class(${class}) is abstract or interface or is not sub_class_of Model::class.", $err_log);
                    continue;
                }
                /* ======== 创建类，获取table、connection、database属性 ======== */
                $modelInfo = $this->getModelProperties($class);
                $connection = $modelInfo['connection'];
                $database = $modelInfo['database'];
                $table = $modelInfo['table'];
                /* ======== 检查表是否存在 ======== */
                if (!$this->dbSelect($connection, sprintf(self::SQL_QUERY_TABLE, $table, $database), true)) {
                    $this->errLog($index, "no this table(${table}) in connection(${connection}).", $err_log);
                    continue;
                }
                /* ======== 获取数字库字段信息，并生成注释信息 ======== */
                $columnInfo = $this->dbSelect($connection, sprintf(self::SQL_QUERY_COLUMN, $table, $database));
                $comments = $this->createComments($columnInfo, function () use ($fileInfo) {
                    return ' * Class ' . $fileInfo['info']['class_name'] . "\n";
                }, function () use ($fileInfo) {
                    return ' * @package ' . $fileInfo['info']['namespace'] . "\n";
                });
                
                $crown = $fileInfo['content']['crown'];
                $below = $fileInfo['content']['below'];
                
                /* ======== 匹配注释列, 并删除类注释 ======== */
                preg_match_all("#(?:<\\?php(?:\\n)+)?/[*]{2}(.*?)\\*/\\n#s", $crown, $crown_no_com);
                
                if ($matches = $crown_no_com[0]) {
                    $comment_match = $matches[count($matches) - 1];
                    if (strpos($comment_match, '<?php') === false) {
                        $crown = str_replace($comment_match,'', $crown);
                    }
                }
                
                $content = $crown.$comments.$below;
                file_put_contents($file_path, $content);
            }
        }
        return $err_log;
        
    }
    
    
    /*---------------------------------------------- info function ----------------------------------------------*/
    
    /**
     * 创建注释信息
     * @param array $list
     * @param \Closure $head
     * @param \Closure $tail
     * @return string
     */
    public function createComments(array $list, \Closure $head = null, \Closure $tail = null)
    {
        $comments = "/**\n";
        $head && $comments .= call_user_func($head);
        foreach ($list as $info) {
            $v_type = $this->match($info->data_type);
            $comments .= " * @property "
                . str_pad($v_type, strlen($this->columnMaxString), ' ')
                . " $" . str_pad($info->column_name, 20, ' ')
                . ' ' . $info->comment . "\n";
        }
        $tail && $comments .= call_user_func($tail);
        $comments .= "*/\n";
        return $comments;
    }
    
    /**
     * 匹配并返回数据类型
     * @param string $string
     * @return int|string
     */
    public function match($string)
    {
        foreach ($this->columnMatch as $key => $match) {
            $type = $match['type'];
            $match = $match['match'];
            if ($type === self::COLUMN_TYPE_STR_POS) {
                if (!(strpos($string, $match) === false)) {
                    return $key;
                }
            }else {
                $find = preg_match($match, $string, $result);
                if ($find) {
                    return $key;
                }
            }
        }
        return $this->columnMatchDefault;
    }
    
    /**
     * 获取模型数据连接属性
     * @param string|Model $model
     * @return array
     */
    protected function getModelProperties($model)
    {
        if (!is_object($model)) {
            $model = new $model;
        }
        return [
            'table' => $model->getTable(),
            'connection' => $model->getConnection()->getConfig('name'),
            'database' => $model->getConnection()->getDatabaseName()
        ];
    }
    
    /**
     * 获取文件信息(类及数据切分)
     * @param $file_path
     * @return array|bool
     */
    protected function getFileInfo(string $file_path)
    {
        $content = file_get_contents($file_path);
        $file_name = basename($file_path);
        $class_name = rtrim($file_name, '.php');
        
        /* ======== 查询命名空间 ======== */
        preg_match("#(?:namespace)(.*);#", $content, $namespace_arr);
        
        /* ======== 查询类分割class及上部分 ======== */
        preg_match("#(.*?(?=(?:class|abstract|interface) .*))(.*)#s", $content, $explode_arr);
        
        $namespace = trim($namespace_arr[1] ?? '');
        $class = $namespace.'\\'.$class_name;
        if (!class_exists($class)) {
            return false;   // 指定类不存在,返回false
        }
        return [
            'file' => [
                'file_path' => $file_path,
                'file_dir' => dirname($file_path),
                'file_name' => $file_name
            ],
            'info' => [
                'class' => $class,
                'namespace' => $namespace,
                'class_name' => $class_name
            ],
            'content' => [
                'content' => $content,
                'crown' => $explode_arr[1],
                'below' => $explode_arr[2]
            ]
        ];
        
    }
    
    /**
     * 读取目录下所有文件名
     * @param string $dir
     * @param array $files
     * @return array
     */
    protected function readAllFilesInDir (string $dir, $files = []){
        if(!is_dir($dir)) return [];
        if($handle = opendir($dir)) {
            while (($fl = readdir($handle)) !== false) {
                $temp = $dir . DIRECTORY_SEPARATOR . $fl;
                //如果不加  $fl!='.' && $fl != '..'  则会造成把$dir的父级目录也读取出来
                if (is_dir($temp) && !in_array($fl, ['.', '..'])) {
                    /* ======== 目录 ======== */
                    $files = array_merge_recursive($files, $this->readAllFilesInDir($temp));
                } else {
                    if (!in_array($fl, ['.', '..'])) {
                        /* ======== 文件 ======== */
                        $files[] = $temp;
                    }
                }
            }
        }
        return $files;
    }
    
    /*---------------------------------------------- query function ----------------------------------------------*/
    
    /**
     * 执行sql
     * @param string $connection    连接库
     * @param string $sql           执行的sql
     * @param bool $is_select_one   是否只查询一条数据
     * @return mixed
     */
    protected function dbSelect(string $connection, string $sql, bool $is_select_one = false)
    {
        $db = DB::connection($connection);
        return $is_select_one ? $db->selectOne($sql) : $db->select($sql);
    }
    
    /**
     * @param int $index
     * @param string $err_msg
     * @param array $log
     * @return array
     */
    protected function errLog($index, $err_msg, &$log = null)
    {
        $err_log = [
            'index' => $index,
            'err_msg' => $err_msg
        ];
        if(isset($log)) {
            $log[] = $err_log;
        }
        return $err_log;
    }
    
}