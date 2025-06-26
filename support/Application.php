<?php

namespace support;

use think\App;
use think\Console as BaseConsole;
use think\exception\Handle;
use think\Request as BaseRequest;

class Application extends App {

    private array $_bind = [
        BaseRequest::class          => Request::class,
        Handle::class => ExceptionHandle::class,
        BaseConsole::class   =>  Console::class,
    ];
    /**
     * 架构方法
     * @access public
     * @param string $rootPath 应用根目录
     */
    public function __construct(string $rootPath = '') {
        parent::__construct($rootPath);
        $this->bind($this->_bind);   
    }

    /**
     * 加载应用文件和配置
     * @access protected
     * @return void
     */
    protected function load(): void {
        parent::load();
        $file = $this->rootPath . 'bootstrap/services.php';
        if (is_file($file)) {
            $services = include $file;
            foreach ($services as $service) {
                $this->register($service);
            }
        }
    }
}