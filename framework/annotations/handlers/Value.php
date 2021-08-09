<?php


namespace Frame\annotations\handlers;


return [
    \Frame\annotations\Value::class=>function($instance, \ReflectionProperty $prop, $prop_annotation){
        $env = parse_ini_file(ROOT_PATH."/.env");
        if(isset($env[$prop_annotation->name]) && $env[$prop_annotation->name]!=""){
            $prop->setValue($instance, $env[$prop_annotation->name]);
        }
    }
];