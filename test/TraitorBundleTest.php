<?php
/*
 * This file is part of the  TraitorBundle, an RunOpenCode project.
 *
 * (c) 2017 RunOpenCode
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace RunOpenCode\Bundle\Traitor\Tests\DependencyInjection\Utils;

use PHPUnit\Framework\TestCase;
use RunOpenCode\Bundle\Traitor\DependencyInjection\CompilerPass;
use RunOpenCode\Bundle\Traitor\DependencyInjection\Extension;
use RunOpenCode\Bundle\Traitor\TraitorBundle;
use Symfony\Component\DependencyInjection\Compiler\PassConfig;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class TraitorBundleTest extends TestCase
{
    /**
     * @var TraitorBundle
     */
    protected $bundle;

    public function setUp()
    {
        parent::setUp();
        $this->bundle = new TraitorBundle();
    }

    /**
     * @test
     */
    public function itProvidesExtension()
    {
        $this->assertInstanceOf(Extension::class, $this->bundle->getContainerExtension());
    }

    /**
     * @test
     */
    public function itRegistersCompilerPass()
    {
        $container = $this
            ->getMockBuilder(ContainerBuilder::class)
            ->getMock();

        $container
            ->expects($spy = $this->any())
            ->method('addCompilerPass');

        $this->bundle->build($container);

        /**
         * @var \PHPUnit_Framework_MockObject_Invocation_Object $invocation
         */
        $invocation = $spy->getInvocations()[0];

        $this->assertEquals(2, count($invocation->parameters));
        $this->assertInstanceOf(CompilerPass::class, $invocation->parameters[0]);
        $this->assertEquals(PassConfig::TYPE_OPTIMIZE, $invocation->parameters[1]);
    }
}
