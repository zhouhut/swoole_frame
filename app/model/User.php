<?php


namespace App\model;

use think\Model;
use think\facade\Db;

/**
 * @\Frame\annotations\DB
 */
class User extends Model
{

    public static function setC(){
//        var_dump($GLOBALS);
//        var_dump(Db::getConfig());
//        Db::setConfig($GLOBALS['db']);

    }

}