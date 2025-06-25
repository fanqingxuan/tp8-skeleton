<?php

namespace support;

use think\App;

class Application extends App {


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