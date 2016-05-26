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

use Symfony\Component\Routing\RouterInterface;

trait RouterAwareTrait
{
    /**
     * @var RouterInterface
     */
    protected $router;

    public function setRouter(RouterInterface $router)
    {
        $this->router = $router;
    }
}
