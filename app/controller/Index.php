<?php

namespace app\controller;

use app\service\HelloService;
use extend\Controller;
use extend\MYLog;
use extend\Result;
use think\facade\Log;

class Index extends Controller
{
    public function index()
    {
        return  $this->ok("hello world");
    }

    public function hello($name = 'ThinkPHP8')
    {
        MYLog::info("hello world",[11,22,33,44]);
        return 'hello,' . $name;
    }

    public function test(HelloService $helloService)
    {
        MYLog::error("error标题",['name'=>'测试','age'=>23,'str'=>'0','a'=>'','null']);
        MYLog::info("info标题","测试了");
        MYLog::debug("debug标题",'测试debug this isdebug');
        return $this->ok($helloService->getUserList());
    }

    public function list() {
        return $this->fail("操作失败");
    }
}
