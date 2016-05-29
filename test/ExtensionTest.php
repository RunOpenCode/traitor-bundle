<?php

namespace RunOpenCode\Bundle\Traitor\Tests;

use Matthias\SymfonyDependencyInjectionTest\PhpUnit\AbstractExtensionTestCase;
use RunOpenCode\Bundle\Traitor\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Extension\ExtensionInterface;

class ExtensionTest extends AbstractExtensionTestCase
{
    /**
     * @test
     */
    public function loadCommonTraitsInInjectionMap()
    {
        $this->load(array(
            'use_common_traits' => true
        ));

        $this->assertContainerBuilderHasParameter('roc.traitor.injection_map', array(
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
        ));
    }

    /**
     * @test
     */
    public function setFilters()
    {
        $this->load(array(
            'filters' => array(
                'namespaces' => array(
                    'RunOpenCode\Bundle\TestNamespace1',
                    'RunOpenCode\Bundle\TestNamespace2'
                ),
                'tags' => array(
                    'form.type'
                )
            )
        ));

        $this->assertContainerBuilderHasParameter('roc.traitor.filter.namespaces', array(
            'RunOpenCode\Bundle\TestNamespace1',
            'RunOpenCode\Bundle\TestNamespace2'
        ));

        $this->assertContainerBuilderHasParameter('roc.traitor.filter.tags', array(
            'form.type'
        ));
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
