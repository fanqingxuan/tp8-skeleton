<?php
declare (strict_types = 1);

namespace app\service;

use app\model\User;

class HelloService
{
    public function getUserList() {
        return [
            ['id' => 1, 'name' => 'thinkphp'],
            ['id' => 2, 'name' => 'hyperf'],
            ['id' => 3, 'name' => 'swoole'],
        ];
    }
}
