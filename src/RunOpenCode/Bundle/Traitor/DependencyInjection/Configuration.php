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

use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * Class Configuration
 *
 * @package RunOpenCode\Bundle\Traitor\DependencyInjection
 */
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
            ->fixXmlConfig('inject')
            ->children()
                ->booleanNode('use_common_traits')
                    ->defaultFalse()
                    ->info('For sake of productivity, some of the common Symfony and vendor traits, as well as traits from this library, can be automatically added to "inject" definition.')
                ->end()
                ->append($this->getInjectionsDefinition())
                ->append($this->getFiltesDefinition())
                ->append($this->getExclusionsDefinition())
            ->end();

        return $treeBuilder;
    }

    /**
     * Configure injections
     *
     * @return ArrayNodeDefinition
     */
    protected function getInjectionsDefinition()
    {
        $node = new ArrayNodeDefinition('injects');

        $node
            ->useAttributeAsKey('trait')
            ->prototype('array')
                ->fixXmlConfig('call')
                ->children()
                    ->arrayNode('calls')
                        ->prototype('array')
                        ->fixXmlConfig('argument')
                            ->children()
                                ->scalarNode('method')->isRequired()->end()
                                ->arrayNode('arguments')
                                    ->prototype('array')
                                        ->beforeNormalization()
                                        ->ifString()
                                        ->then(function ($value) {
                                            return [
                                                'value' => $value
                                            ];
                                        })
                                        ->end()
                                        ->children()
                                            ->scalarNode('id')->defaultNull()->end()
                                            ->scalarNode('type')->defaultValue('string')->end()
                                            ->variableNode('value')->defaultNull()->end()
                                            ->enumNode('on_invalid')->values([ 'ignore', null, 'exception' ])->defaultValue('exception')->end()
                                        ->end()
                                    ->end()
                                ->end()
                            ->end()
                        ->end()
                    ->end()
                ->end()
            ->end();

        return $node;
    }

    /**
     * Configure filters
     * 
     * @return ArrayNodeDefinition
     */
    protected function getFiltesDefinition()
    {
        $node = new ArrayNodeDefinition('filter');

        $node
            ->info('Analyse services for injection which matches filter criteria.')
            ->fixXmlConfig('tag')
            ->fixXmlConfig('namespace')
                ->children()
                    ->arrayNode('tags')
                        ->prototype('scalar')->end()
                    ->end()
                    ->arrayNode('namespaces')
                        ->prototype('scalar')->end()
                    ->end()
                ->end();

        return $node;
    }

    /**
     * Configure exclusions
     *
     * @return ArrayNodeDefinition
     */
    protected function getExclusionsDefinition()
    {
        $node = new ArrayNodeDefinition('exclude');

        $node
            ->info('Exclude services from injection which matches filter criteria.')
            ->fixXmlConfig('service')
            ->fixXmlConfig('namespace')
            ->fixXmlConfig('class', 'classes')
            ->fixXmlConfig('tag')
                ->children()
                    ->arrayNode('services')
                        ->prototype('scalar')->end()
                    ->end()
                    ->arrayNode('namespaces')
                        ->prototype('scalar')->end()
                    ->end()
                    ->arrayNode('classes')
                        ->prototype('scalar')->end()
                    ->end()
                    ->arrayNode('tags')
                        ->prototype('scalar')->end()
                    ->end()
                ->end();

        return $node;
    }
}
