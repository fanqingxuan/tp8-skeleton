<?php

namespace app\request;

use app\validate\HelloValidate;
use support\RequestVo;

class HelloRequest extends RequestVo{

    /**
     *
     */
    public string $name;

    /**
     *
     * @var \app\request\Item[]
     */
    public $list;


    public function validate() {
        return HelloValidate::class;
    }
}