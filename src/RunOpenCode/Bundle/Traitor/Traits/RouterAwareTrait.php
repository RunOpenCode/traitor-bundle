<?php
/*
 * This file is part of the  TraitorBundle, an RunOpenCode project.
 *
 * (c) 2017 RunOpenCode
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace RunOpenCode\Bundle\Traitor\Traits;

use Symfony\Component\Routing\RouterInterface;

/**
 * Class RouterAwareTrait
 *
 * @package RunOpenCode\Bundle\Traitor\Traits
 *
 * @codeCoverageIgnore
 */
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
