<?php

namespace Omatech\Editora\Admin\Events;

class AdminJoinAllEvent
{
    public $instanceId;

    public function __construct(int $instanceId)
    {
        $this->instanceId = $instanceId;
    }
}