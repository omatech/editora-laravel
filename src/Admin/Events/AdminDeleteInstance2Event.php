<?php

namespace Omatech\Editora\Admin\Events;

class AdminDeleteInstance2Event
{
    public $instanceId;

    public function __construct(int $instanceId)
    {
        $this->instanceId = $instanceId;
    }
}