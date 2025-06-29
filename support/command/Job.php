<?php

namespace support\command;

use think\console\command\Make;

class Job extends Make
{
    protected $type = "Job";

    protected function configure()
    {
        parent::configure();
        $this->setName('make:job')
            ->setDescription('Create a new job class');
    }

    protected function getStub(): string
    {
        return __DIR__ . DIRECTORY_SEPARATOR . 'stubs' . DIRECTORY_SEPARATOR . 'job.stub';
    }

    protected function getNamespace(string $app): string
    {
        return parent::getNamespace($app) . '\\job';
    }
}
