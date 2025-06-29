<?php
declare (strict_types = 1);

namespace app\request;

use app\request\validate\HelloValidate;
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

    /**
     *
     * @var null|UploadedFile
     */
    public $image;

    public function initialize(array $data = [])
    {
        $this->image = $this->getRequest()->file("image");
    }

    // 验证器类
    public function validator():string {
        return HelloValidate::class;
    }
}
