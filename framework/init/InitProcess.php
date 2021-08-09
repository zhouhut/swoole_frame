<?php
namespace Frame\init;


use Frame\hepler\FileHelper;
use Swoole\Process;

class InitProcess
{
    private $md5file;
    public function run(){
        return new Process(function (){
            while (true){
                sleep(5);
                $md5_value = FileHelper::getFileMd5(ROOT_PATH. '/app/*','/app/config').FileHelper::getFileMd5(ROOT_PATH. '/framework/*','/app/config');
                if(!$this->md5file) {
                    $this->md5file = $md5_value;
                    continue;
                }

                if(strcmp($this->md5file, $md5_value) != 0){
                    echo "文件已更新". PHP_EOL ;
                    $master_pid = intval(file_get_contents('./swoole.pid'));
                    Process::kill($master_pid, SIGUSR1);
                    $this->md5file = $md5_value;
                    echo '服务已重新加载'.PHP_EOL;
                }
            }
        });
    }

}