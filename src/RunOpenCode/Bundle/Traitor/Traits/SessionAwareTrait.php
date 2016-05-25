<?php

namespace RunOpenCode\Bundle\Traitor\Traits;

use Symfony\Component\HttpFoundation\Session\SessionInterface;

trait SessionAwareTrait
{
    protected $session;

    public function setSession(SessionInterface $session)
    {
        $this->session = $session;
    }
}
