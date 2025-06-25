<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006~2025 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------

namespace support\command;

use think\console\command\Make;

class Request extends Make
{
    protected $type = "Request";

    protected function configure()
    {
        parent::configure();
        $this->setName('make:request')
            ->setDescription('Create a Request class');
    }

    protected function getStub(): string
    {
        $stubPath = __DIR__ . DIRECTORY_SEPARATOR . 'stubs' . DIRECTORY_SEPARATOR;

        return $stubPath . 'request.stub';
    }

    protected function getNamespace(string $app): string
    {
        return parent::getNamespace($app) . '\\request';
    }
}
