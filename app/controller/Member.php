<?php
namespace App\controller;


use Frame\annotations\Classes;
use Frame\http\Request;

/**
 * @Classes
 */
class Member
{
    public function index(Request $request){
        // 获取传来的参数
        $param = $request->getQueryParams();
        return $param;
    }

}