<?php


namespace Frame\annotations;

/**
 * @Annotation
 * @Target({"METHOD"})
 */
class Router
{
    public $value = ""; // 路径
    public $method = []; // 请求方法

}