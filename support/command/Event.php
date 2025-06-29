<?php

namespace support\command;

use think\console\command\Make;

class Event extends Make
{
    protected $type = "Event";

    protected function configure()
    {
        parent::configure();
        $this->setName('make:event')
            ->setDescription('Create a new event class');
    }

    protected function getStub(): string
    {
        return __DIR__ . DIRECTORY_SEPARATOR . 'stubs' . DIRECTORY_SEPARATOR . 'event.stub';
    }

    protected function getNamespace(string $app): string
    {
        return parent::getNamespace($app) . '\\event';
    }
}
