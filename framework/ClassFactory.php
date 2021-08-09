<?php
namespace Frame;

use DI\ContainerBuilder;
use Doctrine\Common\Annotations\AnnotationReader;
use Doctrine\Common\Annotations\AnnotationRegistry;
use Doctrine\Common\Annotations\AnnotationException;
use Frame\init\Env;

class ClassFactory
{
    private static $env = []; // 配置文件
    private static $container; // ioc 容器
    private static $handlers=[]; // 处理方法

    public static function init(){
        // 加载配置文件
        self::$env = Env::getEnv();
//        var_dump(self::$env);


        // 容器构造器
        $builder = new ContainerBuilder();
        $builder->useAnnotations(true);
        self::$container = $builder->build();
//        var_dump(self::$container);

        $handlers =glob(ROOT_PATH.'/framework/annotations/handlers/*.php');
        foreach ($handlers as $handler){
            self::$handlers = array_merge(self::$handlers, require_once($handler));
        }
//        var_dump(self::$handlers);

        $loader = require __DIR__ . '/../vendor/autoload.php';
        AnnotationRegistry::registerLoader([$loader, 'loadClass']);
        $scans = [
            ROOT_PATH."/framework/init" => "Frame\\",
            self::getEnv("scan_dir",ROOT_PATH."/app") => self::getEnv("scan_root_namespace", "App\\")
        ];
//        var_dump($scans);
////        exit;
//        return;
        foreach ($scans as $scan_dir => $scan_root_namespace){
            self::ScanBeans($scan_dir,$scan_root_namespace);
        }

    }

    public static function getClass($key=""){
        try {
            return self::$container->get($key);
        }catch (\ErrorException $exception){
            return false;
        }
    }

    /**
     * 获取配置文件参数
     * @param $key
     * @param string $default
     * @return mixed|string
     */
    private static function getEnv($key, $default=''){
//        if(isset(self::$env[$key])) return self::$env[$key];
//
//        return $default;
//
        return Env::getEnv($key, $default);
    }

    /**
     * 递归获取并引入php文件
     * @param $dir
     * @return array
     */
    public static function getFilesByDir($dir){
        $ret = [];

        $files = glob($dir.'/*',GLOB_MARK);

        foreach ($files as $file){
            if(is_dir($file)){
                $ret = array_merge($ret, self::getFilesByDir($file));
            } else if(pathinfo($file)['extension'] == 'php'){
                $ret[] =$file;
            }
        }
        return $ret;
    }

    public static function ScanBeans($scan_dir,$scan_root_namespace){
        $files = self::getFilesByDir($scan_dir);
//        var_dump(($files));
        foreach ($files as $file){
            require_once $file;
        }

        $reader=new  AnnotationReader();
        $classes = get_declared_classes();

        foreach ($classes as $class){
            if(strstr($class, $scan_root_namespace) && !strstr($class,$scan_root_namespace."annotations")){
//                var_dump($class);
                $reflClass = new \ReflectionClass($class);
//                var_dump($reflClass);
                $classAnnotations = $reader->getClassAnnotations($reflClass);


//                var_dump($classAnnotations);
                foreach ($classAnnotations as $classAnnotation){

                    $instance =self::$container->get($reflClass->getName());

                    // 处理属性注解
                    self::handlerPropsAnnotation($instance, $reflClass, $reader);

                    // 方法注解处理(主要是路由预处理)
                    self::handlerMethodAnnotation($instance, $reflClass, $reader);

                    // 类注解处理
                    self::handlerClassAnnotation($instance, $reflClass, $classAnnotation);


                }
            }
        }
    }

    // 处理属性注解
    public static function handlerPropsAnnotation($instance, \ReflectionClass $reflectionClass, AnnotationReader $annotationReader){
        $props = $reflectionClass->getProperties();
        if(!$props) return ;

        foreach ($props as $prop){
            $prop_annotations = $annotationReader->getPropertyAnnotations($prop);
            foreach ($prop_annotations as $prop_annotation){
                if(!(isset(self::$handlers[get_class($prop_annotation)]))) continue;
                $handler = self::$handlers[get_class($prop_annotation)];
                $handler($instance,$prop, $prop_annotation);
            }
        }
    }

    // 给方法函数创建路由
    public static function handlerMethodAnnotation($instance, \ReflectionClass $reflectionClass, AnnotationReader $annotationReader){
        $methods = $reflectionClass->getMethods();
//        var_dump(get_class($instance));
        if(get_class($instance) == "App\controller\UserController"){
//            var_dump($methods);
        }
        foreach ($methods as $method){
            $annotations = $annotationReader->getMethodAnnotations($method);
            if(get_class($instance) == "App\controller\UserController" && $method->name == 'test'){
//                var_dump($annotations);
            }
            if($annotations){
                foreach ($annotations as $annotation){
                    if(!isset(self::$handlers[get_class($annotation)])) continue;
                    $handler = self::$handlers[get_class($annotation)];
                    $handler($instance, $method, $annotation);
                }
            } else if(strstr(get_class($instance), 'App\controller')){
                //对App\controller 文件夹里的控制器做特殊处理
//                var_dump((get_class($instance)));
                if(isset(self::$handlers[\Frame\annotations\Router::class])){
                    $handler = self::$handlers[\Frame\annotations\Router::class];
                    $handler($instance, $method, $annotation, false);
                }
            }

        }

    }


    // 处理类注解
    public static function handlerClassAnnotation($instance, \ReflectionClass $reflectionClass, $classAnnotation){
        // 获取注解类
        $annotation_class = get_class($classAnnotation);
        if(!isset(self::$handlers[$annotation_class])) return;
        $handler = self::$handlers[$annotation_class];
        $handler($instance, self::$container, $classAnnotation);
    }

}