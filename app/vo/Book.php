<?php
namespace app\vo;

use support\ValueObject;

class Book extends ValueObject{
    
    public string $title;
    public int $status;
}