<?php
namespace extend\core;

use Ramsey\Uuid\Uuid;
use think\App;
use think\log\driver\File;

class MYFileLog extends File {

    private static $traceId;
     // 实例化并传入参数
     public function __construct(App $app, $config = []) {
        parent::__construct($app, $config);
        $this->init();
     }

     public function init() {
        if(!self::$traceId) {
            self::$traceId = Uuid::uuid4();
        }
     }
     /**
     * 日志写入接口
     * @access public
     * @param array $log 日志信息
     * @return bool
     */
    public function save(array $log): bool
    {
        $destination = $this->getMasterLogFile();

        $path = dirname($destination);
        !is_dir($path) && mkdir($path, 0755, true);

        $info = [];

        // 日志信息封装
        $time = \DateTime::createFromFormat('0.u00 U', microtime())->setTimezone(new \DateTimeZone(date_default_timezone_get()))->format($this->config['time_format']);

        foreach ($log as $level => $val) {
            $level_str = strtoupper($level);
            $message = [];
            foreach ($val as $msg) {
                if (!is_string($msg)) {
                    $msg = var_export($msg, true);
                }

                $message[] = $this->config['json'] ?
                json_encode(['time' => $time,'traceid'=>self::$traceId, 'type' => $level_str, 'msg' => $msg], $this->config['json_options']) :
                $time.' | '.sprintf($this->config['format'], self::$traceId, $level_str, $msg);
            }

            if (true === $this->config['apart_level'] || in_array($level, $this->config['apart_level']) || in_array($level_str, $this->config['apart_level'])) {
                // 独立记录的日志级别
                $filename = $this->getApartLevelFile($path, $level);
                $this->write($message, $filename);
                continue;
            }

            $info[$level_str] = $message;
        }

        if ($info) {
            return $this->write($info, $destination);
        }

        return true;
    }
}