<?php
namespace support\middleware;

use support\Result;
use think\Response;

class ResponseMiddleware {
    /**
     * 处理请求
     *
     * @param \think\Request $request
     * @param \Closure       $next
     * @return Response
     */
    public function handle($request, \Closure $next):Response
    {
        /** @var Response $response */
        $response = $next($request);
        $data = $response->getData();
        if($data instanceof Result) {
            $code = $data->getCode();
            return json($data->toArray())->code($code);
        }
        return $response;
    }
}