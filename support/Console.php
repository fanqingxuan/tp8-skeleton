<?php
namespace support;

use support\command\Request;
use think\Console as ThinkConsole;

class Console extends ThinkConsole {

    protected $_commands = [
        Request::class
    ];
    /**
     * 加载指令
     * @access protected
     */
    protected function loadCommands(): void
    {
        parent::loadCommands();

        $this->loadUserCommands(__DIR__."/command");
        $this->loadUserCommands($this->app->getAppPath()."command");

    }



    protected function loadUserCommands(string $dir) {
        if(!$dir){
            return;
        }
        $_dir = rtrim($dir,DIRECTORY_SEPARATOR).DIRECTORY_SEPARATOR;
        $file_list = glob($_dir."*.php");
        $path = $this->app->getRootPath();
        $command_list = array_map(function($file) use ($path) {
            return str_replace(
                [$path, '/', '.php'],
                ['','\\', ''],
                $file
            );
        },$file_list);
        if(!$command_list) {
            return;
        }
        $this->addCommands($command_list);
    }

}