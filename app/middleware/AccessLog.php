<?php
declare (strict_types = 1);

namespace app\middleware;

use think\facade\Log;

class AccessLog
{
    /**
     * 处理请求
     *
     * @param \think\Request $request
     * @param \Closure       $next
     * @return Response
     */
    public function handle($request, \Closure $next)
    {
        $startTime = microtime(true);
        $response = $next($request);
        $endTime = microtime(true);
        $executionTime = $endTime - $startTime;

        $logData = [
            $request->method(),
            $response->getCode(),
            $request->ip(),
            sprintf("%.6fs",$executionTime), 
            $request->header('User-Agent'),
            urldecode($request->url())
        ];
        // 将日志写入数据库或文件，这里以文件为例
        Log::channel('accesslog')->info(implode(' | ', $logData));
        return $response;
    }
}
