<?php
namespace Frame\init;

class Env
{
    public static function getEnv($key="", $default="", $is = false){

        if($key == ""){
            $ret = parse_ini_file(ROOT_PATH. '/.env', true);
            return $ret;
        }
        $ret = getenv(strtoupper(str_replace('.', '_', $key)));
//        if($is){
//            var_dump([$key, $ret]);
//        }

        if($ret === false || $ret == "") return $default;


        if($ret === 'false'){
            $ret = false;
        } elseif($ret === 'true'){
            $ret = true;
        }
        return $ret;

    }

}