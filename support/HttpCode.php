<?php
namespace support;

/**
 * 响应码枚举类
 * 定义常用的响应码和对应的消息
 */
enum HttpCode: int
{
    // 成功响应码
    case SUCCESS = 200;
    case CREATED = 201;
    case ACCEPTED = 202;
    case NO_CONTENT = 204;

    // 客户端错误响应码
    case BAD_REQUEST = 400;
    case UNAUTHORIZED = 401;
    case FORBIDDEN = 403;
    case NOT_FOUND = 404;
    case METHOD_NOT_ALLOWED = 405;
    case CONFLICT = 409;
    case UNPROCESSABLE_ENTITY = 422;
    case TOO_MANY_REQUESTS = 429;

    // 服务器错误响应码
    case INTERNAL_SERVER_ERROR = 500;
    case NOT_IMPLEMENTED = 501;
    case BAD_GATEWAY = 502;
    case SERVICE_UNAVAILABLE = 503;

    /**
     * 获取响应码对应的默认消息
     */
    public function getMessage(): string
    {
        return match($this) {
            // 成功消息
            self::SUCCESS => '操作成功',
            self::CREATED => '创建成功',
            self::ACCEPTED => '请求已接受',
            self::NO_CONTENT => '无内容',

            // 客户端错误消息
            self::BAD_REQUEST => '请求参数错误',
            self::UNAUTHORIZED => '未授权访问',
            self::FORBIDDEN => '禁止访问',
            self::NOT_FOUND => '资源不存在',
            self::METHOD_NOT_ALLOWED => '请求方法不允许',
            self::CONFLICT => '资源冲突',
            self::UNPROCESSABLE_ENTITY => '请求参数验证失败',
            self::TOO_MANY_REQUESTS => '请求过于频繁',

            // 服务器错误消息
            self::INTERNAL_SERVER_ERROR => '服务器内部错误',
            self::NOT_IMPLEMENTED => '功能未实现',
            self::BAD_GATEWAY => '网关错误',
            self::SERVICE_UNAVAILABLE => '服务不可用',
        };
    }

    /**
     * 判断是否为成功响应码
     */
    public function isSuccess(): bool
    {
        return $this->value >= 200 && $this->value < 300;
    }

    /**
     * 判断是否为客户端错误响应码
     */
    public function isClientError(): bool
    {
        return $this->value >= 400 && $this->value < 500;
    }

    /**
     * 判断是否为服务器错误响应码
     */
    public function isServerError(): bool
    {
        return $this->value >= 500 && $this->value < 600;
    }

    /**
     * 判断是否为错误响应码
     */
    public function isError(): bool
    {
        return $this->isClientError() || $this->isServerError();
    }
} 