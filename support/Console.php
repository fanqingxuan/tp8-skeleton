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

        $this->loadCommandsByDirectory();

    }

    protected function loadCommandsByDirectory() {
        $this->addCommands($this->_commands);

        $file_list = glob($this->app->getAppPath()."command/*.php");
        $path = $this->app->getRootPath();
        $command_list = array_map(function($file) use ($path) {
            return str_replace(
                [$path, '/', '.php'],
                ['','\\', ''],
                $file
            );
        },$file_list);
        if($command_list) {
            $this->addCommands($command_list);
        }
    }

}