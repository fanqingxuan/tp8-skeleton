<?php
namespace support;

use think\facade\App;
use think\Request;

class RequestVo extends ValueObject {

    const PARSE_FROM_GET = 1;
    const PARSE_FROM_POST = 2;
    const PARSE_FROM_ALL = 3;

    const DEFAULT_PARSE_TYPE = self::PARSE_FROM_ALL;

    public function __construct(array $data = [])
    {
        switch(static::DEFAULT_PARSE_TYPE) {
            case self::PARSE_FROM_GET:
                $this->parseFromQuery();
                break;
            case self::PARSE_FROM_POST: 
                $this->parseFromPost();
                break;
            default:
                $this->parse();
        }
    }

    public function getRequest():Request {
        return  App::make(Request::class);
    }

    protected function parse() {
        $this->initialize($this->getRequest()->param());
    }
    protected function parseFromPost() {
        $this->initialize($this->getRequest()->post());
    }
    protected function parseFromQuery() {
        $this->initialize($this->getRequest()->get());
    }
}