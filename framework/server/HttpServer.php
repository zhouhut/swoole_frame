<?php
namespace Frame\server;

use Swoole\Http\Server;

class HttpServer
{
    private $server;
    private $dispatcher;
    private $debug;

    /*
      Master进程：主进程 (用于处理swoole的核心事件驱动,一个应用只有一个该进程)
      Manger进程：管理进程 (由Master进程创建, 一个应用只有一个该进程)
      Worker进程：工作进程 (由Manager进程创建, 一个应用可以有多个该进程)
      Task进程：异步任务工作进程 (由Manager进程创建, 一个应用可以有多个该进程)
    */

    /**
     * HttpServer constructor.
     */
    public function __construct($port,$daemonize = false, $debug = false)
    {
        $this->server = new Server('0.0.0.0', $port);
        $this->server->set([
            'worker_num' => 1,
            'daemonize' =>$daemonize
        ]);
        $this->debug = $debug;



        $this->server->on('Request', [$this, 'onRequest']);
        $this->server->on('Start', [$this, 'onStart']);
        $this->server->on('ManagerStart', [$this, 'onManagerStart']);
        $this->server->on('WorkerStart', [$this, 'onWorkerStart']);
//        var_dump(getmypid());

        if($daemonize) {
            var_dump(getmypid());
            file_put_contents('./swoole.pid', intval(getmypid()) + 1);
        }
    }




    // 启动后在主进程（master）的主线程回调此函数
    public function onStart(Server $server){
        cli_set_process_title('swoole_frame master ');
        echo '启动Master主进程';
        echo PHP_EOL;
        $master_id = $server->master_pid;
        file_put_contents('./swoole.pid', $master_id);

    }

    // 当管理进程启动时触发此事件
    public function onManagerStart(Server $server){
        cli_set_process_title('swoole_frame manager ');
        echo '启动Manager管理进程';
        echo PHP_EOL;
    }



    // 此事件在 Worker 进程 / Task 进程 启动时发生
    public function onWorkerStart(Server $server, int $workerId){
        cli_set_process_title('swoole_frame worker');
        echo '启动worker工作进程';
        echo PHP_EOL;
        \Frame\ClassFactory::init();
        $this->dispatcher = \Frame\ClassFactory::getClass('Router')->getsimpleDispatcher();
    }

    public function onRequest($request, $response){


        // 这里处理一下/favicon.ico的问题
//      var_dump($server['path_info']);
        $my_request = \Frame\http\Request::init($request);
        $my_response = \Frame\http\Response::init($response);
        $server = $my_request->getServer();
        if($server['path_info'] == '/favicon.ico' || $server['request_uri'] == '/favicon.ico'){
            $response->end();
            return;
        }



        $routeInfo = $this->dispatcher->dispatch($my_request->getMethod(), $my_request->getUri());
//        var_dump($routeInfo);
        switch ($routeInfo[0]){
            case \FastRoute\Dispatcher::NOT_FOUND:
                $my_response->writeHttpStatus('404');
                $my_response->setBody('404');
                $my_response->end();
                break;
            case \FastRoute\Dispatcher::METHOD_NOT_ALLOWED:
                $allowedMethods = $routeInfo[1];

                // ... 405 Method Not Allowed
                $my_response->writeHttpStatus('405');
                $my_response->setBody('405');
                $my_response->end();
                break;

            case \FastRoute\Dispatcher::FOUND:
                $handler = $routeInfo[1];
                $vars = $routeInfo[2];
                $ext_vars = [$my_request, $my_response];
                $my_response->setBody($handler($vars, $ext_vars));
                $my_response->end();
                // ... call $handler with $vars
                break;

        }
    }

    public function run(){
        // 开发环境下代码热更新
        if($this->debug){
            $process = new \Frame\init\InitProcess();
            $this->server->addProcess($process->run());
        }

        $this->server->start();
    }

}