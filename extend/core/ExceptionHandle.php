<?php
namespace extend\core;

use Exception;
use think\db\exception\DataNotFoundException;
use think\db\exception\DbException;
use think\db\exception\ModelNotFoundException;
use think\exception\Handle;
use think\exception\HttpException;
use think\exception\HttpResponseException;
use think\exception\ValidateException;
use think\Response;
use Throwable;

/**
 * 应用异常处理类
 */
class ExceptionHandle extends Handle
{
    /**
     * 不需要记录信息（日志）的异常类列表
     * @var array
     */
    protected $ignoreReport = [
        HttpException::class,
        HttpResponseException::class,
        ModelNotFoundException::class,
        DataNotFoundException::class,
        ValidateException::class,
    ];

    /**
     * 记录异常信息（包括日志或者其它方式记录）
     *
     * @access public
     * @param  Throwable $exception
     * @return void
     */
    public function report(Throwable $exception): void
    {
        if ($this->isIgnoreReport($exception)) {
            return;
        }
        $data = [
            'file'    => $exception->getFile(),
            'line'    => $exception->getLine(),
            'message' => $this->getMessage($exception),
            'code'    => $this->getCode($exception),
        ];
        $log = "[{$data['code']}]{$data['message']}[{$data['file']}:{$data['line']}]";

        if ($exception instanceof DbException) {
            $exception_data = $exception->getData();
            if(isset($exception_data['Database Status']) && isset($exception_data['Database Status']['Error SQL']) && $exception_data['Database Status']['Error SQL']) {
                $log = "[{$data['code']}]{$data['message']},last sql:{$exception_data['Database Status']['Error SQL']}";
            }
        }
        $log .= PHP_EOL . $exception->getTraceAsString();
        try {
            $this->app->log->record($log, 'error');
        } catch (Exception $e) {}
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @access public
     * @param \think\Request   $request
     * @param Throwable $e
     * @return Response
     */
    public function render($request, Throwable $e): Response
    {
        if ($this->app->isDebug()) {
            return parent::render($request, $e);
        }
        return json([
            'code' => 500,
            'msg' => '服务器内部错误',
            'data' => null
        ])->code(500);
    }
    
}
