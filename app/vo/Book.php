<?php
namespace app\vo;

use app\enums\Status;
use support\ValueObject;

class Book extends ValueObject{
    
    /**
     *
     * @var string
     */
    public $title;

    /**
     *
     * @var \app\enums\Status[]
     */
    public $status;
}