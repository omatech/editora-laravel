<?php

namespace Omatech\Editora\Admin\Events;

class EditStaticTextUpdatedEvent
{
    public $key;

    public function __construct(string $key)
    {
        $this->key = $key;
    }
}