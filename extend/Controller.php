<?php
declare (strict_types = 1);

namespace extend;

use League\Fractal\Manager;
use League\Fractal\Resource\Collection;
use League\Fractal\Resource\Item;
use League\Fractal\TransformerAbstract;
use RuntimeException;
use think\App;
use think\exception\ValidateException;
use think\response\Json;
use think\Validate;

/**
 * 控制器基础类
 */
abstract class Controller
{
    /**
     * Request实例
     * @var \think\Request
     */
    protected $request;

    /**
     * 应用实例
     * @var \think\App
     */
    protected $app;

    /**
     * 是否批量验证
     * @var bool
     */
    protected $batchValidate = false;

    /**
     * 控制器中间件
     * @var array
     */
    protected $middleware = [];

    /**
     * 构造方法
     * @access public
     * @param  App  $app  应用对象
     */
    public function __construct(App $app)
    {
        $this->app     = $app;
        $this->request = $this->app->request;

        // 控制器初始化
        $this->initialize();
    }

    // 初始化
    protected function initialize()
    {}

    /**
     * 验证数据
     * @access protected
     * @param  array        $data     数据
     * @param  string|array $validate 验证器名或者验证规则数组
     * @param  array        $message  提示信息
     * @param  bool         $batch    是否批量验证
     * @return array|string|true
     * @throws ValidateException
     */
    protected function validate(array $data, string|array $validate, array $message = [], bool $batch = false)
    {
        if (is_array($validate)) {
            $v = new Validate();
            $v->rule($validate);
        } else {
            if (strpos($validate, '.')) {
                // 支持场景
                [$validate, $scene] = explode('.', $validate);
            }
            $class = false !== strpos($validate, '\\') ? $validate : $this->app->parseClass('validate', $validate);
            $v     = new $class();
            if (!empty($scene)) {
                $v->scene($scene);
            }
        }

        $v->message($message);

        // 是否批量验证
        if ($batch || $this->batchValidate) {
            $v->batch(true);
        }

        return $v->failException(true)->check($data);
    }


    private function createData($resource,$transformClass='',$isCollection=true) {
        if(!$transformClass) {
            return $resource;
        }
        if(!class_exists($transformClass)) {
            throw new RuntimeException('transformClass类'.$transformClass.'不存在');
        }
        if(!is_subclass_of($transformClass,TransformerAbstract::class)) {
            throw new RuntimeException('transformClass类'.$transformClass.'必须继承 TransformerAbstract');
        }
        if($isCollection) {
            $resource = new Collection($resource,app()->make($transformClass));
        } else {
            $resource = new Item($resource,app()->make($transformClass));
        }
        $fractal = new Manager();
        $fractal->setSerializer(new ArraySerializer());
        return $fractal->createData($resource)->toArray();
    }

    protected function Ok($resource,$transformClass='',$isCollection=true):Json {
        return json(Result::Ok($this->createData($resource,$transformClass,$isCollection))->toArray());
    }

    protected function Fail(string $message,int $code = Result::FAIL_CODE):Json {
        return json(Result::Fail($message, $code)->toArray());
    }

    protected function result(Result $result):Json {
        return json($result->toArray());
    }
}
