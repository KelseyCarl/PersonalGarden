<?php
//定义当前录入时间
define("local_ip","http://".$_SERVER['SERVER_NAME'].':'.$_SERVER["SERVER_PORT"]);
//return array(
//	//'配置项'=>'配置值'
//	'DB_TYPE'   => 'mysql', // 数据库类型
//	'DB_HOST'               => '127.0.0.1', // 服务器地址、
//	'DB_NAME'               => 'mygarden',          // 数据库名
//	'DB_USER'               => 'root',      // 用户名
//	'DB_PWD'                => '',          // 密码
//	'DB_PORT'               => '3306',        // 端口
//	'DB_PREFIX'             => '',    // 数据库表前缀
//	'SHOW_PAGE_TRACE' =>false,  //
//	'SHOW_ERROR_MSG' =>    false,
//	'CACHE_NAME'			=>	'smcp',		//ACE缓存名称
//	'Cache_TimeOut_Token'	=>	60*60*24*15,		//token缓存时间，单位：秒
//	'ERROR_MESSAGE'  =>    '发生错误！',
//	'REDIS_URL'=>'127.0.0.1',//redis的url
//	'REDIS_PWD'=>''
//);
//阿里云服务器的
//return array(
//	'DB_TYPE'      			=>  'mysql',     										// 数据库类型
//	'DB_HOST'      		 =>  'bdm268275596.my3w.com',	// 测试数据库地址
//	'DB_NAME'      			=>  'bdm268275596_db',     									// 数据库名
//	'DB_USER'      			=>  'bdm268275596',     									// 用户名
//	'DB_PWD'       			=>  'HelloWorld112',     								// 密码
//	'DB_PORT'      			=>  '3306',     										// 端口
//	'DB_PREFIX'   		 	=>  '',     											// 数据库表前缀
//	'DB_CHARSET'   			=>  'utf8' 											// 数据库的编码 默认为utf8
////	'DB_FIELDS_CACHE'		=>	false,												//关闭数据表字段缓存(或手动清空 Data/_fields)
//);

//本地localhost的:
//return array(
//	'DB_TYPE'      			=>  'mysql',     										// 数据库类型
//	'DB_HOST'      		 =>  'localhost',	// 测试数据库地址
//	'DB_NAME'      			=>  'mygarden',     									// 数据库名
//	'DB_USER'      			=>  'root',     									// 用户名
//	'DB_PWD'       			=>  '',     								// 密码
//	'DB_PORT'      			=>  '3306',     										// 端口
//	'DB_PREFIX'   		 	=>  '',     											// 数据库表前缀
//	'DB_CHARSET'   			=>  'utf8' 											// 数据库的编码 默认为utf8
////	'DB_FIELDS_CACHE'		=>	false,												//关闭数据表字段缓存(或手动清空 Data/_fields)
//
//);

//阿里云服务器:变更操作系统之后
//return array(
//	'DB_TYPE'      			=>  'mysql',     										// 数据库类型
//	'DB_HOST'      		 =>  'bdm278066211.my3w.com',	// 测试数据库地址
//	'DB_NAME'      			=>  'bdm278066211_db',     									// 数据库名
//	'DB_USER'      			=>  'bdm278066211',     									// 用户名
//	'DB_PWD'       			=>  '1234567890',     								// 密码
//	'DB_PORT'      			=>  '3306',     										// 端口
//	'DB_PREFIX'   		 	=>  '',     											// 数据库表前缀
//	'DB_CHARSET'   			=>  'utf8' 											// 数据库的编码 默认为utf8
////	'DB_FIELDS_CACHE'		=>	false,												//关闭数据表字段缓存(或手动清空 Data/_fields)
//);