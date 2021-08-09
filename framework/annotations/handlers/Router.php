<?php


namespace Frame\annotations\handlers;

use Frame\ClassFactory;

return [
    \Frame\annotations\Router::class=>function($instance, $method, $annotation, $is_router = true){

        if($is_router === true){
            $path = $annotation->value;
            $request_method = count($annotation->method) ? $annotation->method : ['GET'];
        } elseif ($is_router === false){
            $request_method = ['GET', 'POST'];
            $class_name = get_class($instance);
//            var_dump('/无路由规则');
//            var_dump($class_name);
            $path_1 = str_replace("App\controller\\", "", $class_name);
            $path_1 = str_replace("\\", "/", $path_1);
            $path = '/'. $path_1. '/'. $method->name;
            $path = strtolower($path);
        }

//        var_dump($path);
        $router = ClassFactory::getClass('Router');
        $router->addRouter($request_method, $path, function ($params, $ext_params) use($method, $instance){
//            var_dump([$params, $ext_params]);
//            return [$params, $ext_params];
            $input = [];
            $parameters = $method->getParameters();
            foreach ($parameters as $parameter){
                if(isset($params[$parameter->getName()])){
                    $input[] = $params[$parameter->getName()];

                } else {
                    $is = false;
                    foreach ($ext_params  as $ext_param){
                        if($is === true) continue;
                        if($parameter->getClass() && $parameter->getClass()->isInstance($ext_param)){
                            $input[] = $ext_param;
                            $is=true;
                        }
                    }
                    if($is === false)
                        $input[] = false;
                }
            }
            return $method->invokeArgs($instance, $input);

        });
        return $instance;
    }
];