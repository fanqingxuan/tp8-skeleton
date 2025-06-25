<?php
declare (strict_types = 1);

namespace app\request\validate;

use think\Validate;

class HelloValidate extends Validate
{
    /**
     * 定义验证规则
     * 格式：'字段名' =>  ['规则1','规则2'...]
     *
     * @var array
     */
    protected $rule = [
        'name'  =>  'require|min:3',
        "age"   =>  'require|lt:100',
    ];

    /**
     * 定义错误信息
     * 格式：'字段名.规则名' =>  '错误信息'
     *
     * @var array
     */
    protected $message = [
        'name.require' =>  '姓名必填',
        'name.min'  =>  '姓名长度最少是3'
    ];
}
