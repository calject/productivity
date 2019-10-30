<?php
/**
 * Author: 沧澜
 * Date: 2019-10-30
 */

namespace CalJect\Productivity\Constants;


interface MysqlConstant
{
    /**
     * 查询表信息列值信息
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
}