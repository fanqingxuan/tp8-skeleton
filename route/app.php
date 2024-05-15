<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006~2018 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------

use extend\Result;
use think\facade\Route;

Route::get('/','index/index');
Route::get('/hello','index/hello');
Route::get('/show','index/show');

Route::get('think', function () {
    return 'hello,ThinkPHP6!';
});
Route::get('hello/test', 'index/test');

// Route::get('hello/:name', 'index/hello');

Route::get("/list","index/list");

Route::miss(function () {
    return json(Result::fail("页面不存在",404)->toArray())->code(404);
});
