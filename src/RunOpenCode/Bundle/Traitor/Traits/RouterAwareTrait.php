<?php

namespace RunOpenCode\Bundle\Traitor\Traits;

use Symfony\Component\Routing\RouterInterface;

trait RouterAwareTrait
{
    protected $router;

    public function setRouter(RouterInterface $router)
    {
        $this->router = $router;
    }
}
