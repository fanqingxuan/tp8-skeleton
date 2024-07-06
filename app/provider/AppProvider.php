<?php
declare (strict_types = 1);

namespace app\provider;

use extend\core\MYRoute;
use think\facade\Db;
use think\facade\Log;
use think\Route;

/**
 * 应用服务类
 */
class AppProvider extends AbstractProvider
{
    public function register()
    {
        $this->dbListener();
        $this->app->bind(Route::class,MYRoute::class);
    }

    private function dbListener() {
        Db::listen(function($sql, $time, $master) {
            if (str_starts_with($sql, 'CONNECT:')) {
                return;
            }

            // 记录SQL
            if (is_bool($master)) {
                // 分布式记录当前操作的主从
                $master = $master ? 'master|' : 'slave|';
            } else {
                $master = '';
            }

            Log::info($sql . ' [ ' . $master . 'RunTime:' . $time . 's ]');
        });
    }

    public function boot()
    {
        // 服务启动
    }
}
