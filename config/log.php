<?php

// +----------------------------------------------------------------------
// | 日志设置
// +----------------------------------------------------------------------
return [
    // 默认日志记录通道
    'default'      => 'file',
    // 日志记录级别
    'level'        => [],
    // 日志类型记录的通道 ['error'=>'email',...]
    'type_channel' => [],
    // 关闭全局日志写入
    'close'        => false,

    // 日志通道列表
    'channels'     => [
        'accesslog' => [
            // 日志记录方式
            'type'           => '\extend\core\MYFileLog',
            // 日志保存目录
            'path'           => app()->getRuntimePath() . 'log'.DIRECTORY_SEPARATOR.'access',
            // 单文件日志写入
            'single'         => false,
            // 独立日志级别
            'apart_level'    => [],
            // 最大日志文件数量
            'max_files'      => 31,
            // 使用JSON格式记录
            'json'           => false,
            // 日志处理
            // 关闭通道日志写入
            'close'          => false,
            // 日志输出格式化
            'format'         => '%s | %s | %s',
            // 是否实时写入
            'realtime_write' => true,

            'time_format'=>'Y-m-d H:i:s'
        ],

        'file' => [
            // 日志记录方式
            'type'           => '\extend\core\MYFileLog',
            // 日志保存目录
            'path'           => '',
            // 单文件日志写入
            'single'         => false,
            // 独立日志级别
            'apart_level'    => ['error'],
            // 最大日志文件数量
            'max_files'      => 31,
            // 使用JSON格式记录
            'json'           => false,
            // 日志处理
            // 关闭通道日志写入
            'close'          => false,
            // 日志输出格式化
            'format'         => '%s | %s | %s',
            // 是否实时写入
            'realtime_write' => true,
            'time_format'=>'Y-m-d H:i:s'
        ],
        // 其它日志通道配置
    ],

];
