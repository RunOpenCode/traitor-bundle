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

use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * Class DoctrineAwareTrait
 *
 * @package RunOpenCode\Bundle\Traitor\Traits
 *
 * @codeCoverageIgnore
 */
trait DoctrineAwareTrait
{
    /**
     * @var RegistryInterface
     */
    protected $doctrine;

    public function setDoctrine(RegistryInterface $doctrine)
    {
        $this->doctrine = $doctrine;
    }
}
