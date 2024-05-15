<?php

namespace app\request;

use extend\core\Request;
use think\App;

class HelloRequest extends Request
{
    protected const DEFAULT_PARSE_METHOD = ['HEAD','GET'];// 如果定义了的话就从这些方法中解析数据，否则根据请求metho从请求方法中解析

    protected const USE_SNAKE_TO_CAMEL_CASE = true;
    public $myId=55;
    public $host;
    public $cacheControl='status';    

}