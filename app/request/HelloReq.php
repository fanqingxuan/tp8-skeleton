<?php
declare (strict_types = 1);

namespace app\request;

use support\RequestVo;

class HelloReq extends RequestVo
{
    /**
     * @var string
     * @field user_name
     */
    public $name;

    /**
     *
     * @var \app\vo\Book[]
     */
    public $books;

    // 验证器类
    public function validate():string {
        return '';
    }
}
