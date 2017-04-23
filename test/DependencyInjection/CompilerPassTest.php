<?php
/*
 * This file is part of the  TraitorBundle, an RunOpenCode project.
 *
 * (c) 2017 RunOpenCode
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace RunOpenCode\Bundle\Traitor\Tests\DependencyInjection;

use Matthias\SymfonyDependencyInjectionTest\PhpUnit\AbstractCompilerPassTestCase;
use RunOpenCode\Bundle\Traitor\DependencyInjection\CompilerPass;
use RunOpenCode\Bundle\Traitor\Tests\Fixtures\Mocks\Deeper\ThirdService;
use RunOpenCode\Bundle\Traitor\Tests\Fixtures\Mocks\EmptyService;
use RunOpenCode\Bundle\Traitor\Tests\Fixtures\Mocks\FirstService;
use RunOpenCode\Bundle\Traitor\Tests\Fixtures\Mocks\SecondService;
use RunOpenCode\Bundle\Traitor\Tests\Fixtures\Mocks\Traits\FirstTrait;
use RunOpenCode\Bundle\Traitor\Tests\Fixtures\Mocks\Traits\SecondTrait;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\ExpressionLanguage\Expression;

class CompilerPassTest extends AbstractCompilerPassTestCase
{
    public function setUp()
    {
        parent::setUp();

        $firstService = new Definition(FirstService::class);
        $secondService = new Definition(SecondService::class);
        $thirdService = new Definition(ThirdService::class);
        $emptyService = new Definition(EmptyService::class);

        $this->setDefinition('service_one', $firstService);
        $this->setDefinition('service_two', $secondService);
        $this->setDefinition('service_three', $thirdService);
        $this->setDefinition('empty_service', $emptyService);

        $this->setParameter('runopencode.traitor.injectables', [
            FirstTrait::class => [
                [
                    'method' => 'firstTraitFirstMethod',
                    'arguments' => [
                        [
                            'type' => 'service',
                            'id' => 'empty_service',
                            'value' => null,
                            'on_invalid' => 'exception',
                        ],
                        [
                            'type' => 'service',
                            'id' => 'empty_service',
                            'value' => null,
                            'on_invalid' => null,
                        ],
                        [
                            'type' => 'service',
                            'id' => 'empty_service',
                            'value' => null,
                            'on_invalid' => 'ignore',
                        ]
                    ],
                ],
            ],
            SecondTrait::class => [
                [
                    'method' => 'secondTraitFirstMethod',
                    'arguments' => [
                        [
                            'type' => 'string',
                            'id' => null,
                            'value' => 'some_value',
                            'on_invalid' => null,
                        ],
                    ]
                ],
                [
                    'method' => 'secondTraitSecondMethod',
                    'arguments' => [
                        [
                            'type' => 'expression',
                            'id' => null,
                            'value' => 'if (true) false;',
                            'on_invalid' => null,
                        ],
                        [
                            'type' => 'constant',
                            'id' => null,
                            'value' => 'RunOpenCode\\Bundle\\Traitor\\Tests\\Fixtures\\Mocks\\EmptyService::DUMMY_CONSTANT',
                            'on_invalid' => null,
                        ],
                    ]
                ],
            ]
        ]);
    }

    /**
     * @test
     */
    public function itInjectsProperly()
    {
        $this->compile();

        $this->assertContainerBuilderHasServiceDefinitionWithMethodCall('service_three', 'firstTraitFirstMethod', [
            new Reference('empty_service'),
            new Reference('empty_service', ContainerBuilder::NULL_ON_INVALID_REFERENCE),
            new Reference('empty_service', ContainerBuilder::IGNORE_ON_INVALID_REFERENCE)
        ]);

        $this->assertContainerBuilderHasServiceDefinitionWithMethodCall('service_three', 'secondTraitFirstMethod', [
            'some_value'
        ]);
        $this->assertContainerBuilderHasServiceDefinitionWithMethodCall('service_three', 'secondTraitSecondMethod', [
            new Expression('if (true) false;'),
            EmptyService::DUMMY_CONSTANT
        ]);

        $this->assertEquals(0, count($this->container->getDefinition('service_one')->getMethodCalls()));
        $this->assertEquals(0, count($this->container->getDefinition('service_two')->getMethodCalls()));
        $this->assertEquals(0, count($this->container->getDefinition('empty_service')->getMethodCalls()));
    }


    /**
     * {@inheritdoc}
     */
    protected function registerCompilerPass(ContainerBuilder $container)
    {
        $container
            ->addCompilerPass(new CompilerPass());
    }
}
