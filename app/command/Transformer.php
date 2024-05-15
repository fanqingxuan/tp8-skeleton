<?php

namespace app\command;

use think\console\command\Make;

class Transformer extends Make
{
    protected $type = "Transformer";

    protected function configure()
    {
        parent::configure();
        $this->setName('make:transformer')
            ->setDescription('Create a new Transformer class');
    }

    protected function getStub(): string
    {
        return __DIR__ . DIRECTORY_SEPARATOR . 'stubs' . DIRECTORY_SEPARATOR . 'transformer.stub';
    }

    protected function getNamespace(string $app): string
    {
        return parent::getNamespace($app) . '\\transformer';
    }
}
