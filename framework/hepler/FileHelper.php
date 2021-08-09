<?php


namespace Frame\hepler;


class FileHelper
{
    public static function getFileMd5($dir, $ignore){
        // 获取目录下的所有文件\
//        var_dump($dir);
        $files = glob($dir);
        $ret = [];
        foreach ($files as $file) {
            if(is_dir($file) && strpos($file,$ignore) === false){
                $ret[] = self::getFileMd5($file. "/*",$ignore);
            } else if(pathinfo($file)['extension'] == 'php') {
                $ret[] = md5_file($file);
            }
        }
        return !$ret ? '000': md5(implode("", $ret));
    }

}