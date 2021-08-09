<?php


namespace App\controller;

use Frame\annotations\Classes;
use Frame\annotations\Router;
use App\model\User;
use think\facade\Db;

/**
 * @Classes(class_name="users")
 */
class UserController
{

    /**
     * @Router(value="/user/index", method={"GET"})
     */
    public function index(){
        return 'Hello';
    }

    // 没有路由注释
    public function test(){
        $user = User::select();
        return $user;
    }

    /**
     * @Router (value="/user/db", method={"GET"})
     */
    public function db(){
        $user = User::select();
        return $user;
    }


}