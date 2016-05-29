<?php
/*
 * This file is part of the  TraitorBundle, an RunOpenCode project.
 *
 * (c) 2016 RunOpenCode
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace RunOpenCode\Bundle\Traitor\Tests;

use Matthias\SymfonyDependencyInjectionTest\PhpUnit\AbstractCompilerPassTestCase;
use RunOpenCode\Bundle\Traitor\DependencyInjection\CompilerPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;

class CompilerPassTest extends AbstractCompilerPassTestCase
{
    public function setUp()
    {
        parent::setUp();

        require_once __DIR__ . '/mocks.php';
    }

    /**
     * @test
     */
    public function matchNamespaceFilter()
    {
        $this->registerDummyServices();

        $this->setParameter('roc.traitor.filter.namespaces', array(
            '\\Test\\Deeper\\NamespacePrefix\\'
        ));

        $this->compile();

        $this->assertContainerBuilderHasServiceDefinitionWithMethodCall(
            'service_three',
            'setLogger',
            array(
                new Reference('logger')
            )
        );


        $this->assertSame(0, count($this->container->findDefinition('service_one')->getMethodCalls()));
        $this->assertSame(0, count($this->container->findDefinition('service_two')->getMethodCalls()));
    }

    /**
     * @test
     */
    public function matchTagFilter()
    {
        $this->registerDummyServices();

        $this->setParameter('roc.traitor.filter.tags', array(
            'test.tag'
        ));

        $this->container->findDefinition('service_three')->addTag('test.tag', array());

        $this->compile();

        $this->assertContainerBuilderHasServiceDefinitionWithMethodCall(
            'service_three',
            'setLogger',
            array(
                new Reference('logger')
            )
        );

        $this->assertSame(0, count($this->container->findDefinition('service_one')->getMethodCalls()));
        $this->assertSame(0, count($this->container->findDefinition('service_two')->getMethodCalls()));
    }

    /**
     * @test
     */
    public function matchAll()
    {
        $this->registerDummyServices();

        $this->compile();

        $this->assertContainerBuilderHasServiceDefinitionWithMethodCall(
            'service_one',
            'setLogger',
            array(
                new Reference('logger')
            )
        );

        $this->assertContainerBuilderHasServiceDefinitionWithMethodCall(
            'service_two',
            'setLogger',
            array(
                new Reference('logger')
            )
        );

        $this->assertContainerBuilderHasServiceDefinitionWithMethodCall(
            'service_three',
            'setLogger',
            array(
                new Reference('logger')
            )
        );
    }

    /**
     * {@inheritdoc}
     */
    protected function registerCompilerPass(ContainerBuilder $container)
    {
        $container
            ->addCompilerPass(new CompilerPass());
    }

    protected function registerDummyServices()
    {
        $service1 = new Definition(\Test\NamespacePrefix\One\ServiceClass1::class);
        $service2 = new Definition(\Test\NamespacePrefix\Two\ServiceClass2::class);
        $service3 = new Definition(\Test\Deeper\NamespacePrefix\Three\ServiceClass3::class);

        $this->setDefinition('service_one', $service1);
        $this->setDefinition('service_two', $service2);
        $this->setDefinition('service_three', $service3);

        $this->setParameter('roc.traitor.injection_map', array(
            'Psr\Log\LoggerAwareTrait' => array('setLogger', array('@logger'))
        ));
    }
}

