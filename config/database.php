<?php
use Frame\init\Env;

return [
    'default'    =>    'mysql',
    'connections'    =>    [
        'mysql'    =>    [
            // 数据库类型
            'type'        => 'mysql',
            // 服务器地址
            'hostname'    => Env::getEnv('database.hostname','127.0.0.1'),
            // 数据库名
            'database'    => Env::getEnv('database.database','testdata'),
            // 数据库用户名
            'username'    => Env::getEnv('database.username','testdata'),
            // 数据库密码
            'password'    =>  Env::getEnv('database.password','test'),
            // 数据库连接端口
            'hostport'    => Env::getEnv('database.hostport','3308'),
            // 数据库连接参数
            'params'      => [],
            // 数据库编码默认采用utf8
            'charset'     => 'utf8',
            // 数据库表前缀
            'prefix'      => Env::getEnv('database.prefix','eb_')
        ],
    ],
];