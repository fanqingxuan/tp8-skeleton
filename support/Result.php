<?php
namespace support;

class Result {

    const CODE_OK = 0;
    const CODE_ERROR = 1;

    const MESSAGE_OK = '操作成功';
    const MESSAGE_ERROR = '操作失败';

    private $code;
    private $message;
    private $data;

    public function __construct(int $code, string $message, $data = []) {
        $this->code = $code;
        $this->message = $message;
        $this->data = $data;
    }

    public function code(int $code) {
        $this->code = $code;
        return $this;
    }

    public function message(string $message) {
        $this->message = $message;
        return $this;
    }
    
    public function data($data) {
        $this->data = $data;
        return $this;
    }

    public function getCode():int {
        return $this->code;
    }

    public function isOk():bool {
        return $this->code == self::CODE_OK;
    }

    public function isError():bool {
        return $this->code != self::CODE_OK;
    }
    
    public function getData() {
        return $this->data;
    }

    public function getMessage():string {
        return $this->message;
    }

    public function toArray():array {
        return ['code' => $this->code, 'message' => $this->message, 'data' => $this->data];
    }

    public function toJson():string {
        return json_encode($this->toArray(),JSON_UNESCAPED_UNICODE);
    }

}