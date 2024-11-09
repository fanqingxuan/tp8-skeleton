<?php
declare (strict_types = 1);

namespace app\listener;

class HelloListener
{
    /**
     * 事件监听处理
     *
     * @return mixed
     */
    public function handle($payload)
    {
        // dump($payload);
    }
}
