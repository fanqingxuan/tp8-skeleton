<?php
declare (strict_types = 1);

namespace app\command;

use think\console\command\Make;


class Provider extends Make
{
    protected $type = "Provider";

    protected function configure()
    {
        parent::configure();
        $this->setName('make:provider')
            ->setDescription('Create a new Provider class');
    }

    protected function getStub(): string
    {
        return __DIR__ . DIRECTORY_SEPARATOR . 'stubs' . DIRECTORY_SEPARATOR . 'provider.stub';
    }

    protected function getNamespace(string $app): string
    {
        return parent::getNamespace($app) . '\\provider';
    }
}
