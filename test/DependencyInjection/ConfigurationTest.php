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

use Matthias\SymfonyDependencyInjectionTest\PhpUnit\AbstractExtensionConfigurationTestCase;
use RunOpenCode\Bundle\Traitor\DependencyInjection\Configuration;
use RunOpenCode\Bundle\Traitor\DependencyInjection\Extension;

/**
 * Class ConfigurationTest
 *
 * @package RunOpenCode\Bundle\Traitor\Tests\DependencyInjection
 */
class ConfigurationTest extends AbstractExtensionConfigurationTestCase
{
    /**
     * @test
     */
    public function itWillNotLoadCommonTraits()
    {
        $this->assertProcessedConfigurationEquals([
            'use_common_traits' => false,
            'injects' => []
        ], [
            __DIR__ . '/../Fixtures/config/empty-no-common-traits.xml'
        ]);
    }

    /**
     * @test
     */
    public function itWillLoadCommonTraits()
    {
        $this->assertProcessedConfigurationEquals([
            'use_common_traits' => true,
            'injects' => []
        ], [
            __DIR__ . '/../Fixtures/config/empty-with-common-traits.xml'
        ]);
    }

    /**
     * @test
     */
    public function itCanBeFullyConfigured()
    {
        $this->assertProcessedConfigurationEquals([
            'use_common_traits' => true,
            'injects' => [
                'Some\Service\TraitAware' => [
                    'calls' => [
                        [
                            'method' => 'someMethod',
                            'arguments' => [
                                [
                                    'type' => 'service',
                                    'id' => 'some_service',
                                    'value' => null,
                                    'on_invalid' => 'exception',
                                ],
                            ],
                        ],
                        [
                            'method' => 'someOtherMethod',
                            'arguments' => [
                                [
                                    'type' => 'string',
                                    'value' => 'Test',
                                    'id' => null,
                                    'on_invalid' => 'exception',
                                ],
                                [
                                    'type' => 'string',
                                    'value' => '%some.container.parameter%',
                                    'id' => null,
                                    'on_invalid' => 'ignore',
                                ],
                            ],
                        ]
                    ]
                ],
                'Some\Other\TraitAware' => [
                    'calls' => [
                        [
                            'method' => 'someMethod',
                            'arguments' => [
                                [
                                    'type' => 'string',
                                    'value' => 'some argument',
                                    'id' => null,
                                    'on_invalid' => null,
                                ],
                            ]
                        ]
                    ]
                ],
            ],
            'filter' => [
                'tags' => [
                    'some.tag',
                ],
                'namespaces' => [
                    'Some\Namespace',
                    'Some\Other\Namespace',
                ]
            ],
            'exclude' => [
                'services' => [
                    'service.to.exclude',
                ],
                'tags' => [
                    'some.tag.to.exclude',
                ],
                'namespaces' => [
                    'Some\Namespace\To\Exclude',
                ],
                'classes' => [
                    'Exclude\Some\ClassAsWell',
                ],
            ],
        ], [
            __DIR__ . '/../Fixtures/config/full.xml'
        ]);
    }

    /**
     * @test
     */
    public function itCanBeFullyConfiguredViaYaml()
    {
        $this->assertProcessedConfigurationEquals([
            'use_common_traits' => true,
            'injects' => [
                'Some\Service\TraitAware' => [
                    'calls' => [
                        [
                            'method' => 'someMethod',
                            'arguments' => [
                                [
                                    'type' => 'service',
                                    'id' => 'some_service',
                                    'value' => null,
                                    'on_invalid' => 'exception',
                                ],
                            ],
                        ],
                        [
                            'method' => 'someOtherMethod',
                            'arguments' => [
                                [
                                    'type' => 'service',
                                    'id' => 'some_service',
                                    'value' => null,
                                    'on_invalid' => 'ignore',
                                ],
                            ],
                        ],
                    ]
                ],
                'Some\Other\TraitAware' => [
                    'calls' => [
                        [
                            'method' => 'someMethod',
                            'arguments' => [
                                [
                                    'type' => 'string',
                                    'value' => '%parameter%',
                                    'id' => null,
                                    'on_invalid' => 'exception',
                                ],
                                [
                                    'type' => 'string',
                                    'value' => 'string_value',
                                    'id' => null,
                                    'on_invalid' => 'exception',
                                ],
                            ]
                        ],
                        [
                            'method' => 'anotherMethod',
                            'arguments' => [
                                [
                                    'type' => 'string',
                                    'value' => '@escaped_service_string',
                                    'id' => null,
                                    'on_invalid' => 'exception',
                                ],
                            ]
                        ],
                        [
                            'method' => 'yetAnotherMethod',
                            'arguments' => [
                                [
                                    'type' => 'expression',
                                    'value' => 'if(true) false;',
                                    'id' => null,
                                    'on_invalid' => 'exception',
                                ],
                            ]
                        ]
                    ]
                ],
            ],
            'filter' => [
                'tags' => [
                    'some.tag',
                ],
                'namespaces' => [
                    'Some\Namespace',
                    'Some\Other\Namespace',
                ]
            ],
            'exclude' => [
                'services' => [
                    'service.to.exclude',
                ],
                'tags' => [
                    'some.tag.to.exclude',
                ],
                'namespaces' => [
                    'Some\Namespace\To\Exclude',
                ],
                'classes' => [
                    'Exclude\Some\ClassAsWell',
                ],
            ],
        ], [
            __DIR__ . '/../Fixtures/config/full.yml'
        ]);
    }

    /**
     * {@inheritdoc}
     */
    protected function getContainerExtension()
    {
        return new Extension();
    }

    /**
     * {@inheritdoc}
     */
    protected function getConfiguration()
    {
        return new Configuration();
    }
}
