<?php

namespace RunOpenCode\Bundle\Traitor\Traits;

use Symfony\Component\HttpFoundation\RequestStack;

trait RequestStackAwareTrait
{
    protected $requestStack;

    public function setRequestStack(RequestStack $requestStack)
    {
        $this->requestStack = $requestStack;
    }
}
