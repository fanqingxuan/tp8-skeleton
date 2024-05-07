<?php

namespace extend;

class Result {
    private int $code;
    private string $message;
    private mixed $data;

    public const  OK_CODE = 200;
    public const FAIL_CODE = 1;

    private function __construct(){}

    private function __clone(){}

    public static function ok(mixed $data = null,$message='操作成功',$code=self::OK_CODE):static
    {
        $obj = new static;
        $obj->code = $code;
        $obj->message = $message;
        $obj->data = $data;
        return $obj;
    }
    
    public static function fail($message,$code=self::FAIL_CODE):static
    {
        $obj = new static;
        $obj->code = $code;
        $obj->message = $message;
        $obj->data = null;
        return $obj;
    }

    public function isOK():bool {
        return $this->code == self::OK_CODE;
    }

    public function isFail():bool {
        return !$this->isOK();
    }

    public function getMessage():string {
        return $this->message;
    }

    public function getData() {
        return $this->data;
    }

    public function toJson():string {
        return json_encode($this->toArray(),JSON_UNESCAPED_UNICODE);
    }

    public function toArray():array {
        return [
            'code' => $this->code,
            'message' => $this->message,
            'data' => $this->data
        ];
    }
}