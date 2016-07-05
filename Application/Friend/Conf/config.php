<?php
/**
 * Api模块的配置文件
 */
return array(	
    'TMPL_CACHE_ON' => false,//禁止模板编译缓存2
	'HTML_CACHE_ON' => false,//禁止静态缓存	
	'DEFAULT_MODULE'=> 'Api',   //默认模块
	"EARTH_RADIUS"	=> 6371,	
	'SHOW_PAGE_TRACE' => 0,//显示调试信息
	'DB_TYPE'  =>  'mysql',            // 数据库类型
    'DB_HOST'  =>  '127.0.0.1',  // 服务器地址
    'DB_NAME'  =>  'danjiguanjia',           // 数据库名
    'DB_USER'  =>  'root',            // 用户名
    'DB_PWD'   =>  'root',              // 密码
	'DB_PORT'   => '3306', // 端口
	'DB_PREFIX' => 'dj_', // 数据库表前缀
); 
