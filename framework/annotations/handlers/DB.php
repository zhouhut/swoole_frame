<?php
namespace Frame\annotations\handlers;

use think\facade\Db;

return [
    \Frame\annotations\DB::class=>function($instance, $container, $self){
        $config = Db::getConfig();
        if(!$config)
            Db::setConfig($GLOBALS['db']);
        $container->set(get_class($instance), $instance);
    }
];