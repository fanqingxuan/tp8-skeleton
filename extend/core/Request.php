<?php
namespace extend\core;

use ReflectionClass;
use ReflectionProperty;
use RuntimeException;
use think\App;
use think\exception\ValidateException;
use think\Request as ThinkRequest;
use think\Validate;

// 应用请求对象类
class Request extends ThinkRequest
{

     // 如果定义了的话就从这些方法中解析数据，否则根据请求metho从请求方法中解析
    protected const DEFAULT_PARSE_METHOD = [];

    // 支持将数据是长蛇格式的数据键值映射到驼峰模式类属性
    protected const USE_SNAKE_TO_CAMEL_CASE = false;

    public static function __make(App $app)
    {
        $request = new static();

        if (function_exists('apache_request_headers') && $result = apache_request_headers()) {
            $header = $result;
        } else {
            $header = [];
            $server = $_SERVER;
            foreach ($server as $key => $val) {
                if (str_starts_with($key, 'HTTP_')) {
                    $key          = str_replace('_', '-', strtolower(substr($key, 5)));
                    $header[$key] = $val;
                }
            }
            if (isset($server['CONTENT_TYPE'])) {
                $header['content-type'] = $server['CONTENT_TYPE'];
            }
            if (isset($server['CONTENT_LENGTH'])) {
                $header['content-length'] = $server['CONTENT_LENGTH'];
            }
        }

        $request->header = array_change_key_case($header);
        $request->server = $_SERVER;
        $request->env    = $app->env;

        $inputData = $request->getInputData($request->input);

        $request->get     = $_GET;
        $request->post    = $_POST ?: $inputData;
        $request->put     = $inputData;
        $request->request = $_REQUEST;
        $request->cookie  = $_COOKIE;
        $request->file    = $_FILES ?? [];
        $request->parsePulibcProperty();
        return $request;
    }

    protected function Validator() {
        return '';
    }



    // 将request的字段解析到类属性中
    private function parsePulibcProperty() {
        if(!is_subclass_of($this, self::class)) {
            return [];
        }
       // 创建一个反射类实例，用于ChildClass
        $reflection = new ReflectionClass($this);
        $data = $this->getTargetData();
        $property_data = [];
        foreach ($reflection->getProperties(ReflectionProperty::IS_PUBLIC) as $property) {
            if(!$property->isPublic()) {
                continue;
            }
            if($property->getDeclaringClass()->getName() != get_class($this)) {
                continue;
            }
            $fieldName = $property->getName();
            if(array_key_exists($fieldName,$data)) {
                $this->{$fieldName} = $data[$fieldName];
            }elseif(static::USE_SNAKE_TO_CAMEL_CASE) {
                $newFieldName = $this->camelCaseToSnakeCase($fieldName);
                if(array_key_exists($newFieldName,$data)) {
                    $this->{$fieldName} = $data[$newFieldName];
                }
            }
            $property_data[$fieldName] = $this->{$fieldName};
        }
        if($this->Validator()) {
            if(!is_subclass_of($this->Validator(),Validate::class)) {
                throw new RuntimeException('Validator must be subclass of think\Validate');
            }
            try {
                validate($this->Validator())->batch(true)->check($property_data);
            } catch (ValidateException $e) {
                // 验证失败 输出错误信息
                throw $e;
            }
        }
    }

    private function camelCaseToSnakeCase($camelCaseString) {
        // 使用 preg_replace 函数找到每个大写字母的前面，添加下划线
        $snakeCaseString = preg_replace('/([a-z])([A-Z])/', '$1_$2', $camelCaseString);
    
        // 将所有字符转为小写
        $snakeCaseString = strtolower($snakeCaseString);
    
        return $snakeCaseString;
    }

    private function getTargetData() {
        $method = static::DEFAULT_PARSE_METHOD;
        $allowMethod = ['GET','POST','HEAD'];
        $methodList = [];
        if($method) {
            $methodList = is_array($method)?$method:[$method];
            if(array_diff($methodList,$allowMethod)) {
                throw new RuntimeException('DEFAULT_PARSE_METHOD must be string of GET,POST,HEAD or empty');
            }
        } else {
            $method = $this->method();
            $method = strtoupper($method);
            if(!in_array($method,$allowMethod)) {
                $methodList = [];
            } else {
                $methodList = [$method];
            }
        }
        $result = [];

        foreach($methodList as $m) {
            switch($m) {
                case 'GET':
                    $data = $this->get;
                    break;
                case 'POST':
                    $data = $this->post;
                    break;
                case 'HEAD':
                    $data = [];
                    foreach($this->header as $key => $value) {
                        $skey = str_replace('-', '_', strtolower($key));
                        $data[$skey] = $value;
                    }
                    break;
            }
            $result = array_merge($result,$data);
        }
        return $result;
    }
}