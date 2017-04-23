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

use RunOpenCode\Bundle\Traitor\Utils\ClassUtils;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\ExpressionLanguage\Expression;

/**
 * Class CompilerPass
 *
 * @package RunOpenCode\Bundle\Traitor\DependencyInjection
 */
class CompilerPass implements CompilerPassInterface
{
    /**
     * @var array
     */
    private $injectables;

    /**
     * @var array
     */
    private $filter;

    /**
     * @var array
     */
    private $exclude;

    /**
     * {@inheritdoc}
     */
    public function process(ContainerBuilder $container)
    {
        if (!$container->hasParameter('runopencode.traitor.injectables')) {
            return;
        }

        $this->injectables = $container->getParameter('runopencode.traitor.injectables');
        $this->filter = [
            'tags' => ($container->hasParameter('runopencode.traitor.filter.tags')) ? array_combine($tags = $container->getParameter('runopencode.traitor.filter.tags'), $tags) : [],
            'namespaces' => ($container->hasParameter('runopencode.traitor.filter.namespaces')) ? $container->getParameter('runopencode.traitor.filter.namespaces') : [],
        ];
        $this->exclude = [
            'tags' => ($container->hasParameter('runopencode.traitor.exclude.tags')) ? array_combine($tags = $container->getParameter('runopencode.traitor.exclude.tags'), $tags) : [],
            'namespaces' => ($container->hasParameter('runopencode.traitor.exclude.namespaces')) ? $container->getParameter('runopencode.traitor.exclude.namespaces') : [],
            'classes' => ($container->hasParameter('runopencode.traitor.exclude.classes')) ? array_combine($classes = $container->getParameter('runopencode.traitor.exclude.classes'), $classes) : [],
            'services' => ($container->hasParameter('runopencode.traitor.exclude.services')) ? array_combine($services = $container->getParameter('runopencode.traitor.exclude.services'), $services) : [],
        ];

        if (0 === count($this->filter['tags']) + count($this->filter['namespaces'])) {
            $this->filter = null;
        }

        if (0 === count($this->exclude['tags']) + count($this->exclude['namespaces']) + count($this->exclude['classes']) + count($this->exclude['services'])) {
            $this->exclude = null;
        }

        $injectableServices = $this->findInjectableServices($container);

        foreach ($injectableServices as $definition) {
            $this->processInjections($definition);
        }
    }

    /**
     * Find all services which should be injected with services via traits.
     *
     * @param ContainerBuilder $container
     * @return array
     */
    private function findInjectableServices(ContainerBuilder $container)
    {
        $services = [];

        foreach ($container->getDefinitions() as $serviceId => $definition) {

            if ($definition->isSynthetic() || !$definition->getClass()) {
                continue;
            }

            if ($this->isInjectable($serviceId, $definition) && !$this->isExcluded($serviceId, $definition)) {
                $services[$serviceId] = $definition;
            }
        }

        return $services;
    }

    /**
     * Check if service definition should be injected with service via traits.
     *
     * @param string $serviceId
     * @param Definition $definition
     * @return bool
     */
    private function isInjectable($serviceId, Definition $definition)
    {
        if (null === $this->filter) {
            return true;
        }

        $class = $definition->getClass();

        foreach ($this->filter['namespaces'] as $namespace) {

            if (ClassUtils::isWithinNamespace($class, $namespace)) {
                return true;
            }
        }

        foreach ($definition->getTags() as $tag) {

            if (isset($tag['name'], $this->filter['tags'][$tag['name']])) {
                return true;
            }
        }

        return false;
    }

    /**
     * Check if service definition should be excluded from service injection via traits.
     *
     * @param string $serviceId
     * @param Definition $definition
     * @return bool
     */
    private function isExcluded($serviceId, Definition $definition)
    {
        if (null === $this->exclude) {
            return false;
        }

        if (isset($this->exclude['services'][$serviceId])) {
            return true;
        }

        $class = $definition->getClass();

        if (isset($this->exclude['classes'][$class])) {
            return true;
        }

        foreach ($this->exclude['namespaces'] as $namespace) {

            if (ClassUtils::isWithinNamespace($class, $namespace)) {
                return true;
            }
        }

        foreach ($definition->getTags() as $tag) {

            if (isset($tag['name'], $this->exclude['tags'][$tag['name']])) {
                return true;
            }
        }

        return false;
    }

    /**
     * Process service injections via traits.
     *
     * @param Definition $definition
     */
    private function processInjections(Definition $definition)
    {
        $class = $definition->getClass();

        foreach ($this->injectables as $trait => $calls) {

            if (class_exists($class) && ClassUtils::usesTrait($class, $trait)) {
                foreach ($calls as $call) {
                    $definition->addMethodCall($call['method'], $this->processArguments($call['arguments']));
                }
            }
        }
    }

    /**
     * Process service injection parameters.
     *
     * @param array $arguments
     * @return array
     */
    private function processArguments(array $arguments)
    {
        $processed = [];

        foreach ($arguments as $argument) {
            $processed[] = $this->{sprintf('process%sAsArgument', ucfirst($argument['type']))}($argument);
        }

        return $processed;
    }

    /**
     * Process service as argument.
     *
     * @param array $argument
     * @return Reference
     */
    private function processServiceAsArgument(array $argument)
    {
        $invalidBehaviour = ContainerBuilder::EXCEPTION_ON_INVALID_REFERENCE;

        if (null === $argument['on_invalid']) {
            $invalidBehaviour = ContainerBuilder::NULL_ON_INVALID_REFERENCE;
        }

        if ('ignore' === $argument['on_invalid']) {
            $invalidBehaviour = ContainerBuilder::IGNORE_ON_INVALID_REFERENCE;
        }

        return new Reference($argument['id'], $invalidBehaviour);
    }

    /**
     * Process expression as argument.
     *
     * @param array $argument
     * @return Expression
     */
    private function processExpressionAsArgument(array $argument)
    {
        return new Expression($argument['value']);
    }

    /**
     * Process string as argument
     *
     * @param array $argument
     * @return string
     */
    private function processStringAsArgument(array $argument)
    {
        return (string) $argument['value'];
    }

    /**
     * Process constant as argument.
     *
     * @param array $argument
     * @return mixed
     */
    private function processConstantAsArgument(array $argument)
    {
        return constant($argument['value']);
    }
}
