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

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension as BaseExtension;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;

class Extension extends BaseExtension
{
    /**
     * {@inheritdoc}
     */
    public function getAlias()
    {
        return 'runopencode_traitor';
    }

    /**
     * {@inheritdoc}
     */
    public function getNamespace()
    {
        return 'http://www.runopencode.com/xsd-schema/traitor-bundle';
    }

    /**
     * {@inheritdoc}
     */
    public function getXsdValidationBasePath()
    {
        return __DIR__.'/../Resources/config/schema';
    }

    /**
     * {@inheritdoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();

        $config = $this->processConfiguration($configuration, $configs);

        if ($config['use_common_traits']) {

            $loader = new XmlFileLoader(
                $container,
                new FileLocator(__DIR__.'/../Resources/config')
            );

            $loader->load('common-traits.xml');

            array_map(function($config) use (&$configs) {
                $configs[] = $config;
            }, $container->getExtensionConfig($this->getAlias()));

            $config = $this->processConfiguration($configuration, $configs);
        }

        $this
            ->processInjections($config, $container)
            ->processFilters($config, $container)
            ->processExclusions($config, $container);
    }

    /**
     * @param array $config
     * @param ContainerBuilder $container
     * @return Extension $this
     */
    protected function processInjections(array $config, ContainerBuilder $container)
    {
        $injection = [];

        foreach ($config['injects'] as $trait => $injection) {
            $injection[ltrim($trait, '\\')] = $injection;
        }

        if (count($injection) > 0) {
            $container->setParameter('runopencode.traitor.injectables', $injection);
        }

        return $this;
    }

    /**
     * @param array $config
     * @param ContainerBuilder $container
     * @return Extension $this
     */
    protected function processFilters(array $config, ContainerBuilder $container)
    {
        if (isset($config['filters'])) {

            if (count($config['filters']['tags']) > 0) {
                $container->setParameter('runopencode.traitor.filter.tags', $config['filters']['tags']);
            }

            if (count($config['filters']['namespaces']) > 0) {

                $container->setParameter('runopencode.traitor.filter.namespaces', array_map(function($namespace) {
                    return rtrim(ltrim($namespace, '\\'), '\\') . '\\';
                }, $config['filters']['namespaces']));
            }
        }

        return $this;
    }

    /**
     * @param array $config
     * @param ContainerBuilder $container
     * @return Extension $this
     */
    protected function processExclusions(array $config, ContainerBuilder $container)
    {
        if (isset($config['exclude'])) {

            if (count($config['exclude']['services']) > 0) {
                $container->setParameter('runopencode.traitor.exclude.services', $config['exclude']['services']);
            }

            if (count($config['exclude']['namespaces']) > 0) {

                $container->setParameter('runopencode.traitor.exclude.namespaces', array_map(function($namespace) {
                    return rtrim(ltrim($namespace, '\\'), '\\') . '\\';
                }, $config['exclude']['namespaces']));
            }

            if (count($config['exclude']['classes']) > 0) {

                $container->setParameter('runopencode.traitor.exclude.classes', array_map(function($fqcn) {
                    return ltrim($fqcn, '\\');
                }, $config['exclude']['classes']));
            }

            if (count($config['exclude']['tags']) > 0) {
                $container->setParameter('runopencode.traitor.exclude.tags', $config['exclude']['tags']);
            }

        }

        return $this;
    }
}

