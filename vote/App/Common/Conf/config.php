<?php
return array(
    //项目配置项
	//'配置项'=>'配置值'
    //开启页面Trace
    //'SHOW_PAGE_TRACE' => true,
    'URL_MODEL' => 2,
    //允许访问的模块
    'MODULE_ALLOW_LIST' => array('Admin','Wx'),
    //默认的模块
    'DEFAULT_MODULE' => 'Admin',
    //伪静态
    'URL_HTML_SUFFIX' => 'html',
    //默认控制器
    'DEFAULT_CONTROLLER' => 'Index',
    //默认的方法
    'DEFAULT_ACTION' => 'index',
    //数据库的连接
    'DB_TYPE'               =>  '',
    'DB_HOST'               =>  '', // 服务器地址
    'DB_NAME'               =>  '',          // 数据库名
    'DB_USER'               =>  '',      // 用户名
    'DB_PWD'                =>  '',          // 密码
    'DB_PORT'               =>  '',        // 端口
    'DB_CHARSET'            =>  '',
);