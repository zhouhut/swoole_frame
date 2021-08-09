<?php


namespace Frame\init;

use Frame\annotations\Classes;
/**
 * @Classes
 */
class Router
{
    public $routers = [];

    /**
     * Router constructor.
     * @param array $routers
     */
    public function __construct()
    {
        $this->routers[] = [
            'method' => 'GET',
            'uri' => '/',
            'called' => function(){
                return '/Index';
            }
        ];
    }

    // 添加路由数组
    public function addRouter($method, $uri, $called){
//        return \FastRoute\simpleDispatcher(function (\FastRoute\RouteCollector $r){
//
//        });
//        var_dump($uri);
        $this->routers[] = [
            'method' => $method,
            'uri' => $uri,
            'called' => $called
        ];
    }

    public function getsimpleDispatcher(){
//        var_dump('getsimpleDispatcher1');
        return  \FastRoute\simpleDispatcher(function(\FastRoute\RouteCollector $r) {
//            var_dump($this->routers);
            foreach ($this->routers as $router){
                $r->addRoute($router['method'], $router['uri'], $router['called']);
            }
        });
    }

    public function ss(){
        return ['ss'];
    }


}