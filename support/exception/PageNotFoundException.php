<?php

namespace support\exception;

class PageNotFoundException extends \Exception {
    public function __construct()
    {
        parent::__construct("请求的资源不存在",404);
    }
}