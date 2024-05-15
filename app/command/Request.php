<?php

namespace app\command;

use think\console\command\Make;

class Request extends Make
{
    protected $type = "Request";

    protected function configure()
    {
        parent::configure();
        $this->setName('make:request')
            ->setDescription('Create a new Request class');
    }

    protected function getStub(): string
    {
        return __DIR__ . DIRECTORY_SEPARATOR . 'stubs' . DIRECTORY_SEPARATOR . 'request.stub';
    }

    protected function getNamespace(string $app): string
    {
        return parent::getNamespace($app) . '\\request';
    }
}
