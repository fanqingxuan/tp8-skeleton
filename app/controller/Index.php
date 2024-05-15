<?php

namespace app\controller;

use app\event\Hello;
use app\model\User;
use app\service\HelloService;
use app\transformer\UserItemTransformer;
use app\transformer\UserTransformer;
use extend\Controller;
use extend\MYLog;
use extend\Result;
use think\facade\Db;
use think\facade\Log;

class Index extends Controller
{
    public function index()
    {
        // event(Hello::class,[new User(),5555]);
        return  $this->Ok("hello world");
    }

    public function hello($name = 'ThinkPHP8')
    {
        MYLog::info("hello world",[11,22,33,44]);
        return $this->Ok([1,2,3,3,5]);
    }

    public function show() {
        $user = [
            'id'=>1,
            'name'=>'thinkphp',
            'age'=>18,
            'address'=>'beijing',
            'hobby'=>['football','basketball'],
        ];
        return $this->Ok($user,UserItemTransformer::class,false);
    }

    public function list()
    {
        $userlist = [
            ['id'=>1,'name'=>'thinkphp','age'=>18],
            ['id'=>2,'name'=>'hyperf','age'=>19],
            ['id'=>3,'name'=>'swoole','age'=>20],
        ];
        return $this->Ok($userlist,UserTransformer::class);
    }

    public function test(HelloService $helloService)
    {
        MYLog::error("error标题",['name'=>'测试','age'=>23,'str'=>'0','a'=>'','null']);
        MYLog::info("info标题","测试了");
        MYLog::debug("debug标题",'测试debug this isdebug');
        return $this->ok($helloService->getUserList());
    }

    
}
