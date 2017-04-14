<?php
/*
 * This file is part of the  TraitorBundle, an RunOpenCode project.
 *
 * (c) 2017 RunOpenCode
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace RunOpenCode\Bundle\Traitor\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritdoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();

        $rootNode = $treeBuilder->root('runopencode_traitor');

        $rootNode
            ->addDefaultsIfNotSet()
            ->children()
                ->booleanNode('use_common_traits')
                    ->defaultFalse()
                    ->info('For sake of productivity, some of the common Symfony and vendor traits, as well as traits from this library, can be automatically added to "inject" definition.')
                ->end()
                ->arrayNode('inject')
                    ->useAttributeAsKey('trait')
                    ->prototype('array')
                        ->validate()
                            ->ifTrue(function($value) {

                                if (!is_array($value) || 2 !== count($value)) {
                                    return true;
                                }

                                if (!is_string($value[0])) {
                                    return true;
                                }

                                if (!is_array($value[1])) {
                                    return true;
                                }

                                return false;
                            })
                            ->thenInvalid('Expected proper setter injection definition.')
                        ->end()
                        ->prototype('variable')
                        ->end()
                    ->end()
                ->end()
                ->arrayNode('filters')
                    ->info('Analyse services for injection which matches filter criteria.')
                    ->children()
                        ->arrayNode('tags')
                            ->prototype('scalar')
                            ->end()
                        ->end()
                        ->arrayNode('namespaces')
                            ->prototype('scalar')
                            ->end()
                        ->end()
                    ->end()
                ->end()
                ->arrayNode('exclude')
                    ->info('Exclude services from injection which matches filter criteria.')
                    ->children()
                        ->arrayNode('services')
                            ->prototype('scalar')
                            ->end()
                        ->end()
                        ->arrayNode('namespaces')
                            ->prototype('scalar')
                            ->end()
                        ->end()
                        ->arrayNode('classes')
                            ->prototype('scalar')
                            ->end()
                        ->end()
                    ->end()
                ->end()
            ->end();

        return $treeBuilder;
    }
}
