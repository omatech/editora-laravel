<?php

namespace Omatech\Editora\Admin\Events;

class AdminDeleteRelationInstanceEvent
{
    public $instanceId;

    public function __construct(int $instanceId)
    {
        $this->instanceId = $instanceId;
    }
}