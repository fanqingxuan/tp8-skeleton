<?php

namespace support\exception;

use support\ErrCode;
use support\Result;

class BusinessException extends \Exception {
    public function __construct(string $message = "", int $code = Result::CODE_ERROR, \Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}