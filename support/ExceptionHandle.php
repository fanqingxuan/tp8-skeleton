<?php
namespace support;

use support\exception\BusinessException;
use think\db\exception\DataNotFoundException;
use think\db\exception\ModelNotFoundException;
use think\exception\Handle;
use think\exception\HttpException;
use think\exception\HttpResponseException;
use think\exception\ValidateException;
use think\Request as Request;
use think\Response;
use think\Validate;
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
        BusinessException::class,
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
        // 使用内置的方式记录异常日志
        parent::report($exception);
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
        // 添加自定义异常处理机制

        // 其他错误交给系统处理
        return parent::render($request, $e);
    }

    /**
     * @access protected
     * @param Throwable $exception
     * @return Response
     */
    protected function convertExceptionToResponse(Request $request, Throwable $exception): Response
    {
        if($this->app->isDebug() && !($exception instanceof ValidateException || $exception instanceof BusinessException)){
            $data = $this->getDebugMsg($exception);
            if($request->isJson()) {
                $response = Response::create($data, 'json');
            } else {
                $response = Response::create($this->renderExceptionByContent($data));
            }
        } else {
            $code = $exception->getCode();
            if($exception instanceof HttpException) {
                $code = $exception->getStatusCode();
                $httpCode = HttpCode::from($code);
                $message = $httpCode->getMessage();
            } elseif($exception instanceof ValidateException) {
                $code = 2;
                $error = $exception->getError();
                $message = is_array($error) ? implode("\n",$error) : $error;
            } elseif($exception instanceof BusinessException) {
                $code = $exception->getCode();
                $message = $exception->getMessage();
            } else {
                $code = 500;
                $message = '服务器内部错误';
            }        
            $response = Response::create(ResponseUtil::error($message)->code($code)->toArray(),'json');
        }
        

        $statusCode = 500;
        if ($exception instanceof HttpException) {
            $statusCode = $exception->getStatusCode();
            $response->header($exception->getHeaders());
        }elseif($exception instanceof ValidateException || $exception instanceof BusinessException) {
            $statusCode = 200;
        }
        return $response->code($statusCode);
    }

    protected function renderExceptionByContent(array $data): string
    {
        ob_start();
        extract($data);
        include $this->app->config->get('app.exception_tmpl') ?: __DIR__ . '/../../tpl/think_exception.tpl';

        return ob_get_clean();
    }
}
