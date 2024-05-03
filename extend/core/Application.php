<?php

namespace extend\core;

use think\App as ThinkApp;

class Application extends ThinkApp {
    /**
     * 架构方法
     * @access public
     * @param string $rootPath 应用根目录
     */
    public function __construct(string $rootPath = '')
    {
        parent::__construct($rootPath);
    }

    /**
     * 加载应用文件和配置
     * @access protected
     * @return void
     */
    protected function load(): void
    {
        $appPath = $this->getAppPath();
        // 加载helper
        $files = glob($appPath . '/helper/*.php');
        foreach($files as $file) {
            include_once $file;
        }
        parent::load();

        if (is_file($this->getConfigPath() . 'provider.php')) {
            $providers_list = include $this->getConfigPath() . 'provider.php';
            foreach ($providers_list as $provider) {
                $this->register($provider);
            }
        }

        
    }
}