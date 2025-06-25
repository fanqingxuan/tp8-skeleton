<?php

namespace app\request;

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
}