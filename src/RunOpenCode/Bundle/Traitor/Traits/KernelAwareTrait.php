<?php

namespace RunOpenCode\Bundle\Traitor\Traits;

use Symfony\Component\HttpKernel\KernelInterface;

trait KernelAwareTrait
{
    protected $kernel;

    public function setKernel(KernelInterface $kernel)
    {
        $this->kernel = $kernel;
    }
}
