<?php

namespace RunOpenCode\Bundle\Traitor\Traits;

use Symfony\Bridge\Doctrine\RegistryInterface;

trait DoctrineAwareTrait
{
    protected $doctrine;

    public function setDoctrine(RegistryInterface $doctrine)
    {
        $this->doctrine = $doctrine;
    }
}
