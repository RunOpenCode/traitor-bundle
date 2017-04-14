<?php
/*
 * This file is part of the  TraitorBundle, an RunOpenCode project.
 *
 * (c) 2017 RunOpenCode
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace RunOpenCode\Bundle\Traitor\Tests\Fixtures\Mocks\Deeper;

use RunOpenCode\Bundle\Traitor\Tests\Fixtures\Mocks\SecondService;
use RunOpenCode\Bundle\Traitor\Tests\Fixtures\Mocks\Traits\SecondTrait;

final class ThirdService extends SecondService
{
    use SecondTrait;
}
