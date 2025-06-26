<?php

namespace app\controller;

use app\request\HelloReq;
use app\transformer\HelloTransformer;
use support\ResponseUtil;

class IndexController 
{
    public function index()
    {
        return '<style>*{ padding: 0; margin: 0; }</style><iframe src="https://www.thinkphp.cn/welcome?version=' . \think\facade\App::version() . '" width="100%" height="100%" frameborder="0" scrolling="auto"></iframe>';
    }

    public function hello($name = 'ThinkPHP8')
    {
        return 'hello,' . $name;
    }

    public function say(HelloReq $req) {
        $list = [
            ['name'=>'张三','age'=>43,'info'=>'河北'],
            ['name'=>'李四','age'=>32,'info'=>'山西'],
        ];
        dump(ResponseUtil::collection($list,HelloTransformer::class,['total'=>43]));
    }
}
