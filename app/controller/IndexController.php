<?php

namespace app\controller;

use app\model\User;
use app\request\HelloReq;
use app\transformer\HelloTransformer;
use support\exception\BusinessException;
use support\facade\DB;
use support\ResponseUtil;

class IndexController 
{
    public function index()
    {
        return '<style>*{ padding: 0; margin: 0; }</style><iframe src="https://www.thinkphp.cn/welcome?version=' . \think\facade\App::version() . '" width="100%" height="100%" frameborder="0" scrolling="auto"></iframe>';
    }

    public function hello($name = 'ThinkPHP8')
    {
        throw new BusinessException("测试错误");
        return 'hello,' . $name;
    }

    public function say(HelloReq $req) {
        DB::table("HELLO")->findOrFail();
        return ResponseUtil::collection($req->books??[],HelloTransformer::class,['total'=>43]);
    }
}
