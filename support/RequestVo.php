<?php
namespace support;

use think\exception\ValidateException;
use think\facade\App;
use think\Request;
use think\Validate;

class RequestVo extends ValueObject {

    const PARSE_FROM_GET = 1;
    const PARSE_FROM_POST = 2;
    const PARSE_FROM_ALL = 3;

    const DEFAULT_PARSE_TYPE = self::PARSE_FROM_ALL;

    public function __construct(array $data = [])
    {
        
        $this->handleValidate();
        parent::__construct($this->getDataFromRequest());
    }

    public function getRequest():Request {
        return  App::make(Request::class);
    }

    protected function getDataFromRequest():array {
        switch(static::DEFAULT_PARSE_TYPE) {
            case self::PARSE_FROM_GET:
                $data = $this->getRequest()->get();
                break;
            case self::PARSE_FROM_POST: 
                $data = $this->getRequest()->post();
                break;
            default:
                $data = $this->getRequest()->param();
        }
        return $data;
        
    }

    protected function handleValidate() {
        if(method_exists($this,'validator')) {
            $class = $this->validator();
            if($class && is_subclass_of($class,Validate::class)) {
                validate($class)->batch(true)->check($this->getDataFromRequest());
            }
        }
    }
}