<?php
/*
 * This file is part of the  TraitorBundle, an RunOpenCode project.
 *
 * (c) 2016 RunOpenCode
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace RunOpenCode\Bundle\Traitor\DependencyInjection;

use RunOpenCode\Bundle\Traitor\Utils\ClassUtils;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;

class CompilerPass implements CompilerPassInterface
{
    /**
     * {@inheritdoc}
     */
    public function process(ContainerBuilder $container)
    {
        if (!$container->hasParameter('roc.traitor.injection_map') || count($container->getParameter('roc.traitor.injection_map')) === 0) {
            return;
        }

        if ($container->hasParameter('roc.traitor.filter.tags') || $container->hasParameter('roc.traitor.filter.namespaces')) {

            $definitions = array_merge(
                $container->hasParameter('roc.traitor.filter.tags') ? $this->getDefinitionsFromTags($container, $container->getParameter('roc.traitor.filter.tags')) : array(),
                $container->hasParameter('roc.traitor.filter.namespaces') ? $this->getDefinitionsFromClassNamespaces($container, $container->getParameter('roc.traitor.filter.namespaces')) : array()
            );

        } else {

            $definitions = $this->getDefinitionsFromClassNamespaces($container, array());
        }

        $definitions = $this->filterExcludedDefinitions($container, $definitions);

        $this->processInjection($definitions, $container->getParameter('roc.traitor.injection_map'));
    }

    /**
     * Get definitions from container based on namespace filter
     *
     * @param ContainerBuilder $container
     * @param array $filters Namespace prefixes
     * @return Definition[] Definitions indexed by service ID
     */
    protected function getDefinitionsFromClassNamespaces(ContainerBuilder $container, array $filters)
    {
        $result = array();

        /**
         * @var Definition $definition
         */
        foreach ($container->getDefinitions() as $id => $definition) {

            $class = $definition->getClass();

            if (count($filters) > 0) {

                $found = false;

                foreach ($filters as $namespace) {

                    if (ClassUtils::isWithinNamespace($class, $namespace)) {
                        $found = true;
                        break;
                    }
                }

                if (!$found) {
                    continue; // Go to next definition
                }
            }

            $result[$id] = $definition;
        }

        return $result;
    }

    /**
     * Get definitions from container based on service tag filter
     *
     * @param ContainerBuilder $container
     * @param array $tags Tag names
     * @return Definition[] Definitions indexed by service ID
     */
    protected function getDefinitionsFromTags(ContainerBuilder $container, array $tags)
    {
        $result = array();

        if (count($tags) > 0) {

            foreach ($tags as $tag) {

                foreach (array_keys($container->findTaggedServiceIds($tag)) as $id) {

                    $result[$id] = $container->getDefinition($id);
                }
            }
        }

        return $result;
    }

    /**
     * Process injection of services via traits for given definitions.
     *
     * @param Definition[] $definitions Definitions to process.
     * @param array $injectionMap Injection map.
     */
    protected function processInjection(array $definitions, array $injectionMap)
    {
        if (count($injectionMap) > 0 && count($definitions) > 0) {

            /**
             * @var Definition $definition
             */
            foreach ($definitions as $definition) {

                $class = $definition->getClass();

                foreach ($injectionMap as $trait => $injectionDefinition) {

                    if (class_exists($class) && ClassUtils::usesTrait($class, $trait)) {

                        $arguments = array();

                        foreach ($injectionDefinition[1] as $argument) {

                            if ('@' === $argument[0]) {
                                $arguments[] = new Reference(ltrim($argument, '@'));
                            } else {
                                $arguments[] = $argument;
                            }
                        }

                        $definition->addMethodCall($injectionDefinition['0'], $arguments);
                    }
                }
            }
        }
    }

    /**
     * Remove excluded definitions from definitions collection.
     *
     * @param ContainerBuilder $container
     * @param Definition[] $definitions
     * @return Definition[]
     */
    protected function filterExcludedDefinitions(ContainerBuilder $container, array $definitions)
    {
        $excludedServices = $container->hasParameter('roc.traitor.exclude.services') ? $container->getParameter('roc.traitor.exclude.services') : null;
        $excludedClasses = $container->hasParameter('roc.traitor.exclude.classes') ? $container->getParameter('roc.traitor.exclude.classes') : null;
        $excludedNamespaces = $container->hasParameter('roc.traitor.exclude.namespaces') ? $container->getParameter('roc.traitor.exclude.namespaces') : null;

        $result = array_filter($definitions, \Closure::bind(function(Definition $definition, $serviceId) use ($excludedServices, $excludedClasses, $excludedNamespaces) {

            if (null !== $excludedServices && in_array($serviceId, $excludedServices, true)) {
                return false;
            }

            $serviceFqcn = ltrim($definition->getClass(), '\\');

            if (null !== $excludedClasses && in_array($serviceFqcn, $excludedClasses, true)) {
                return false;
            }

            if (null !== $excludedNamespaces) {

                foreach ($excludedNamespaces as $excludedNamespace) {

                    if (strpos($serviceFqcn, $excludedNamespace) === 0) {
                        return false;
                    }
                }
            }

            return true;

        }, $this), 1);

        return $result;
    }
}
