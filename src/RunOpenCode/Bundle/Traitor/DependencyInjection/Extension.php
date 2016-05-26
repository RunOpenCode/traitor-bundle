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

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension as BaseExtension;

class Extension extends BaseExtension
{
    /**
     * {@inheritdoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        if ($config['use_common_traits']) {
            $config['inject'] = array_merge($config['inject'], $this->getCommonTraitsInjectionDefinitions());
        }

        $container->setParameter('roc.traitor.injection_map', $config['inject']);

        if (isset($config['filters'])) {

            if (count($config['filters']['tags']) > 0) {
                $container->setParameter('roc.traitor.filter.tags', $config['filters']['tags']);
            }

            if (count($config['filters']['tags']) > 0) {
                $container->setParameter('roc.traitor.filter.namespaces', $config['filters']['namespaces']);
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getAlias()
    {
        return 'run_open_code_traitor';
    }

    /**
     * For sake of productivity, below is the map of commonly used services.
     *
     * @return array Injection map
     */
    protected function getCommonTraitsInjectionDefinitions()
    {
        return array(
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
            'RunOpenCode\Bundle\Traitor\Traits\ValidatorAwareTrait' => array('setValidator', array('@validator'))
        );
    }
}

