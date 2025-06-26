<?php
namespace support;

use support\middleware\ResponseMiddleware;
use think\Http as ThinkHttp;

class Http extends ThinkHttp {
    
    protected $middlewares = [
        ResponseMiddleware::class,
    ];
    /**
     * 加载全局中间件
     */
    protected function loadMiddleware(): void
    {
        parent::loadMiddleware();

        $this->app->middleware->import($this->middlewares);
    }
}