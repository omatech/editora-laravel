<?php

namespace Omatech\Editora\Admin\Events;

class AdminRelationSortEvent
{
    public $instanceId;

    public function __construct(int $instanceId)
    {
        $this->instanceId = $instanceId;
    }
}