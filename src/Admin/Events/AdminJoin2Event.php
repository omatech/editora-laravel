<?php

namespace Omatech\Editora\Admin\Events;

class AdminJoin2Event
{
    public $instanceId;

    public function __construct(int $instanceId)
    {
        $this->instanceId = $instanceId;
    }
}