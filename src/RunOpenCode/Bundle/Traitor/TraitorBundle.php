<?php
/*
 * This file is part of the  TraitorBundle, an RunOpenCode project.
 *
 * (c) 2016 RunOpenCode
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace RunOpenCode\Bundle\Traitor;

use RunOpenCode\Bundle\Traitor\DependencyInjection\Extension;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class TraitorBundle extends Bundle
{
    public function getContainerExtension()
    {
        return new Extension();
    }
}
