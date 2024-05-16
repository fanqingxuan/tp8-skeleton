<?php

namespace app\request\validate;

use think\Validate;

class HelloValidate extends Validate
{
    protected $rule = [
        'id' => 'require|number',
        'userName' => 'require',
    ];

    protected $message = [
        'id.require' => 'id不能为空',
        'id.number' => 'id必须是数字',
        'userName' => '名字不能为空',
    ];
}