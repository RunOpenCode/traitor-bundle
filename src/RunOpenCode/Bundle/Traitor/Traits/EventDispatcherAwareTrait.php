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

use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * Class EventDispatcherAwareTrait
 *
 * @package RunOpenCode\Bundle\Traitor\Traits
 *
 * @codeCoverageIgnore
 */
trait EventDispatcherAwareTrait
{
    /**
     * @var EventDispatcherInterface
     */
    protected $eventDispatcher;

    public function setEventDispatcher(EventDispatcherInterface $eventDispatcher)
    {
        $this->eventDispatcher = $eventDispatcher;
    }
}
