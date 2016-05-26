<?php
/*
 * This file is part of the  TraitorBundle, an RunOpenCode project.
 *
 * (c) 2016 RunOpenCode
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace RunOpenCode\Bundle\Traitor\Traits;

use Symfony\Component\HttpFoundation\Session\SessionInterface;

trait SessionAwareTrait
{
    /**
     * @var SessionInterface
     */
    protected $session;

    public function setSession(SessionInterface $session)
    {
        $this->session = $session;
    }
}
