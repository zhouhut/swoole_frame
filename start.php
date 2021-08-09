<?php
require_once __DIR__ .'/vendor/autoload.php';

define('ROOT_PATH', dirname(__FILE__));

use Frame\server\HttpServer;
use Frame\init\Env;

if($argc < 2) {
    echo '请输入正确的参数';
    echo PHP_EOL;
    exit();
}

if(in_array('start', $argv)){
    // 启动应用

    // 加载配置文件
    $env = parse_ini_file(ROOT_PATH. '/.env',true);

    foreach ($env as $key => $value){
        $name = strtoupper($key);
        if(is_array($value)){
            foreach ($value as $k=>$v){
                $long_name = $name. '_'.strtoupper($k);
                putenv("$long_name=$v");
            }
        } else {
            putenv("$name=$value");
        }
    }

    require_once __DIR__. '/config/app.php';

    $daemonize = false; // 是否启动守护进程
    $debug = false;     // 是否为debug模式, 如果是, 将启动热更新

    $port  = Env::getEnv('swoole_http_port', 9501);      // http服务器端口

    if(in_array('-d', $argv)) $daemonize = true;
    if(in_array('--debug', $argv)) $debug = true;

    $server = new HttpServer($port,$daemonize, $debug);
    $server->run();

} else if(in_array('stop', $argv)){
    // 关闭应用

    $master_pid = intval(file_get_contents('./swoole.pid'));
    if($master_pid && $master_pid > 0){
        \Swoole\Process::kill($master_pid);
    }
} else {
    echo '请输入正确的参数';
    echo PHP_EOL;
    exit();
}


