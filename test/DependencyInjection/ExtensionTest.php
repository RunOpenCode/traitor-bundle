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

use Matthias\SymfonyDependencyInjectionTest\PhpUnit\AbstractExtensionTestCase;
use RunOpenCode\Bundle\Traitor\DependencyInjection\Extension;

class ExtensionTest extends AbstractExtensionTestCase
{
    /**
     * @test
     */
    public function thereAreNoInjectables()
    {
        $this->load([
            'use_common_traits' => false,
        ]);

        $this->assertFalse($this->container->hasParameter('runopencode.traitor.injectables'));
    }

    /**
     * @test
     */
    public function thereAreInjectablesWithinCommonTraits()
    {
        $this->load([
            'use_common_traits' => true,
        ]);

        $this->assertContainerBuilderHasParameter('runopencode.traitor.injectables');
    }

    /**
     * @test
     */
    public function thereAreFilters()
    {
        $this->load([
            'filters' => [
                'namespaces' => [
                    '\\RunOpenCode\\Bundle\\TestNamespace1\\',
                    'RunOpenCode\\Bundle\\TestNamespace2',
                ],
                'tags' => [
                    'form.type',
                ]
            ]
        ]);

        $this->assertContainerBuilderHasParameter('runopencode.traitor.filter.namespaces', [
            'RunOpenCode\\Bundle\\TestNamespace1\\',
            'RunOpenCode\\Bundle\\TestNamespace2\\',
        ]);

        $this->assertContainerBuilderHasParameter('runopencode.traitor.filter.tags', [
            'form.type',
        ]);
    }

    /**
     * @test
     */
    public function thereAreExclusions()
    {
        $this->load([
            'exclude' => [
                'services' => [
                    'service_1',
                    'service_2',
                ],
                'tags' => [
                    'tag.tag_1',
                    'tag.tag_2',
                ],
                'namespaces' => [
                    '\\RunOpenCode\\Bundle\\TestNamespace1\\',
                    'RunOpenCode\\Bundle\\TestNamespace2',
                ],
                'classes' => [
                    '\\RunOpenCode\\Bundle\\TestNamespace1\\ClassX',
                    'RunOpenCode\\Bundle\\TestNamespace2\\ClassY',
                ]
            ]
        ]);

        $this->assertContainerBuilderHasParameter('runopencode.traitor.exclude.services', [
            'service_1',
            'service_2',
        ]);

        $this->assertContainerBuilderHasParameter('runopencode.traitor.exclude.tags', [
            'tag.tag_1',
            'tag.tag_2',
        ]);

        $this->assertContainerBuilderHasParameter('runopencode.traitor.exclude.namespaces', [
            'RunOpenCode\\Bundle\\TestNamespace1\\',
            'RunOpenCode\\Bundle\\TestNamespace2\\',
        ]);

        $this->assertContainerBuilderHasParameter('runopencode.traitor.exclude.classes', [
            'RunOpenCode\\Bundle\\TestNamespace1\\ClassX',
            'RunOpenCode\\Bundle\\TestNamespace2\\ClassY',
        ]);
    }

    /**
     * {@inheritdoc}
     */
    protected function getContainerExtensions()
    {
        return array(
            new Extension()
        );
    }
}
