<?php

namespace RunOpenCode\Bundle\Traitor\Traits;

use Symfony\Component\EventDispatcher\EventDispatcherInterface;

trait EventDispatcherAwareTrait
{
    protected $eventDispatcher;

    public function setEventDispatcher(EventDispatcherInterface $eventDispatcher)
    {
        $this->eventDispatcher = $eventDispatcher;
    }
}
