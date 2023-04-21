<?php

namespace Omatech\Editora\Admin\Events;

class NewInstance2InsertedEvent
{
    public $instanceId;

    public function __construct(int $instanceId)
    {
        $this->instanceId = $instanceId;
    }
}