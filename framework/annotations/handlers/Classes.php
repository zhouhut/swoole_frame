<?php
namespace Frame\annotations\handlers;

// 类注解处理方法

return[
    \Frame\annotations\Classes::class => function($instance, $container, $self){
        $vars = get_object_vars($self);
//        var_dump($vars);
        if(isset($vars['class_name']) && $vars['class_name'] != ""){
            $ClassName = $vars['class_name'];
        } else {
            $arr = explode("\\", get_class($instance));
            $ClassName = end($arr);
        }
//        var_dump($ClassName);
        $container->set($ClassName, $instance);
    }
];