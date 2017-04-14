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

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension as BaseExtension;

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

        $this
            ->setInjectionMapAsContainerParameter($config, $container)
            ->setFiltersAsContainerParameter($config, $container)
            ->setExclusionsAsContainerParameter($config, $container)
            ;
    }

    /**
     * @param array $config
     * @param ContainerBuilder $container
     * @return Extension $this
     */
    protected function setInjectionMapAsContainerParameter(array $config, ContainerBuilder $container)
    {
        $injection = array();

        foreach ($config['inject'] as $trait => $injection) {
            $injection[ltrim($trait, '\\')] = $injection;
        }

        if ($config['use_common_traits']) {

            $injection = array_merge($injection, array(
                'Symfony\Component\DependencyInjection\ContainerAwareTrait' => array('setContainer', array('@service_container')),
                'Psr\Log\LoggerAwareTrait' => array('setLogger', array('@logger')),
                'RunOpenCode\Bundle\Traitor\Traits\DoctrineAwareTrait' => array('setDoctrine', array('@doctrine')),
                'RunOpenCode\Bundle\Traitor\Traits\EventDispatcherAwareTrait' => array('setEventDispatcher', array('@event_dispatcher')),
                'RunOpenCode\Bundle\Traitor\Traits\FilesystemAwareTrait' => array('setFilesystem', array('@filesystem')),
                'RunOpenCode\Bundle\Traitor\Traits\KernelAwareTrait' => array('setKernel', array('@kernel')),
                'RunOpenCode\Bundle\Traitor\Traits\MailerAwareInterface' => array('setMailer', array('@mailer')),
                'RunOpenCode\Bundle\Traitor\Traits\PropertyAccessorAwareTrait' => array('setPropertyAccessor', array('@property_accessor')),
                'RunOpenCode\Bundle\Traitor\Traits\RequestStackAwareTrait' => array('setRequestStack', array('@request_stack')),
                'RunOpenCode\Bundle\Traitor\Traits\RouterAwareTrait' => array('setRouter', array('@router')),
                'RunOpenCode\Bundle\Traitor\Traits\AuthorizationCheckerAwareTrait' => array('setAuthorizationChecker', array('@security.authorization_checker')),
                'RunOpenCode\Bundle\Traitor\Traits\SessionAwareTrait' => array('setSession', array('@session')),
                'RunOpenCode\Bundle\Traitor\Traits\TwigAwareTrait' => array('setTwig', array('@twig')),
                'RunOpenCode\Bundle\Traitor\Traits\TranslatorAwareTrait' => array('setTranslator', array('@translator')),
                'RunOpenCode\Bundle\Traitor\Traits\ValidatorAwareTrait' => array('setValidator', array('@validator')),
                'RunOpenCode\Bundle\Traitor\Traits\TokenStorageAwareTrait' => array('setTokenStorage', array('@security.token_storage'))
            ));
        }

        $container->setParameter('runopencode.traitor.injection_map', $injection);

        return $this;
    }

    /**
     * @param array $config
     * @param ContainerBuilder $container
     * @return Extension $this
     */
    protected function setFiltersAsContainerParameter(array $config, ContainerBuilder $container)
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
    protected function setExclusionsAsContainerParameter(array $config, ContainerBuilder $container)
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
        }

        return $this;
    }
}

