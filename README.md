`swoole_frame`是一款简单的swoole开发框架


## 特点

* 遵循 PSR-4 标准,自动加载类文件
* IOC控制容器
* 引入了第三方路由,并支持注解的形式自定义路由
* 简单的swoole的HttpServer代码封装
* 整合了ORM
* 简单的热更新

## 启动方法
* 进入项目根目录,cli模式下输入
```angular2html
  # php start.php start            // 非守护进程模式
  # php start.php start --debug     // 非守护进程模式, debug模式下，启动热更新
  # php start.php start -d         // 以守护进程的方式启动
  # php start.php start -d --debug  // 以守护进程的方式启动, debug模式下，启动热更新
  # php start.php stop             // 关闭应用
```

## 特别鸣谢

感谢以下的项目,排名不分先后

Doctrine Annotations: https://www.doctrine-project.org/

PHP-DI: https://php-di.org/

FastRoute: https://github.com/nikic/FastRoute

think-orm: https://www.kancloud.cn/manual/think-orm/content







## 版权信息

swoole_frame遵循Apache2开源协议发布，并提供免费使用。

本项目包含的第三方源码和二进制文件之版权信息另行标注。

