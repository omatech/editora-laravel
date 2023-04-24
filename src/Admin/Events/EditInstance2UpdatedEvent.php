<?php

namespace Omatech\Editora\Admin\Events;

class EditInstance2UpdatedEvent
{
    public $instanceId;

    public function __construct(int $instanceId)
    {
        $this->instanceId = $instanceId;
    }
}