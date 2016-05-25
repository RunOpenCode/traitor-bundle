<?php

namespace RunOpenCode\Bundle\Traitor\Traits;

use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

trait AuthorizationCheckerAwareTrait
{
    protected $authorizationChecker;

    public function setAuthorizationChecker(AuthorizationCheckerInterface $authorizationChecker)
    {
        $this->authorizationChecker = $authorizationChecker;
    }
}
