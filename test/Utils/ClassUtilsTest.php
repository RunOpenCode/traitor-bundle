<?php
/*
 * This file is part of the  TraitorBundle, an RunOpenCode project.
 *
 * (c) 2017 RunOpenCode
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace RunOpenCode\Bundle\Traitor\Tests\DependencyInjection\Utils;

use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerAwareTrait;
use RunOpenCode\Bundle\Traitor\Tests\Fixtures\Mocks\Deeper\ThirdService;
use RunOpenCode\Bundle\Traitor\Tests\Fixtures\Mocks\EmptyService;
use RunOpenCode\Bundle\Traitor\Tests\Fixtures\Mocks\FirstService;
use RunOpenCode\Bundle\Traitor\Tests\Fixtures\Mocks\SecondService;
use RunOpenCode\Bundle\Traitor\Tests\Fixtures\Mocks\Traits\FirstTrait;
use RunOpenCode\Bundle\Traitor\Utils\ClassUtils;

class ClassUtilsTest extends TestCase
{
    /**
     * @test
     */
    public function objectUsesTrait()
    {
        $first = new FirstService();
        $second = new SecondService();
        $third = new ThirdService();

        $this->assertTrue(ClassUtils::usesTrait($first, LoggerAwareTrait::class));
        $this->assertTrue(ClassUtils::usesTrait($second, LoggerAwareTrait::class));
        $this->assertTrue(ClassUtils::usesTrait($third, LoggerAwareTrait::class));
        $this->assertFalse(ClassUtils::usesTrait($first, FirstTrait::class));
        $this->assertFalse(ClassUtils::usesTrait($second, FirstTrait::class));
        $this->assertTrue(ClassUtils::usesTrait($third, FirstTrait::class));
    }

    /**
     * @test
     */
    public function classUsesTrait()
    {
        $this->assertTrue(ClassUtils::usesTrait(FirstService::class, LoggerAwareTrait::class));
        $this->assertTrue(ClassUtils::usesTrait(SecondService::class, LoggerAwareTrait::class));
        $this->assertTrue(ClassUtils::usesTrait(ThirdService::class, LoggerAwareTrait::class));
        $this->assertFalse(ClassUtils::usesTrait(FirstService::class, FirstTrait::class));
        $this->assertFalse(ClassUtils::usesTrait(SecondService::class, FirstTrait::class));
        $this->assertTrue(ClassUtils::usesTrait(ThirdService::class, FirstTrait::class));
    }

    /**
     * @test
     * @expectedException \RunOpenCode\Bundle\Traitor\Exception\InvalidArgumentException
     */
    public function classUsesTraitThrowsInvalidArgumentExceptionOnScalar()
    {
        ClassUtils::usesTrait(10, LoggerAwareTrait::class);
    }

    /**
     * @test
     * @expectedException \RunOpenCode\Bundle\Traitor\Exception\RuntimeException
     */
    public function classUsesTraitThrowsRuntimeExceptionOnNonExistingClass()
    {
        ClassUtils::usesTrait('SomeNonExistingClass', LoggerAwareTrait::class);
    }

    /**
     * @test
     */
    public function getTraits()
    {
        $empty = new EmptyService();
        $first = new FirstService();
        $third = new ThirdService();

        $this->assertEquals([], ClassUtils::getTraits($empty));
        $this->assertEquals([
            'Psr\Log\LoggerAwareTrait' => 'Psr\Log\LoggerAwareTrait',
        ], ClassUtils::getTraits($first));
        $this->assertEquals([
            'RunOpenCode\Bundle\Traitor\Tests\Fixtures\Mocks\Traits\FirstTrait' => 'RunOpenCode\Bundle\Traitor\Tests\Fixtures\Mocks\Traits\FirstTrait',
            'Psr\Log\LoggerAwareTrait' => 'Psr\Log\LoggerAwareTrait',
            'RunOpenCode\Bundle\Traitor\Tests\Fixtures\Mocks\Traits\SecondTrait' => 'RunOpenCode\Bundle\Traitor\Tests\Fixtures\Mocks\Traits\SecondTrait',
        ], ClassUtils::getTraits($third));

        $this->assertEquals([], ClassUtils::getTraits(EmptyService::class));
        $this->assertEquals([
            'Psr\Log\LoggerAwareTrait' => 'Psr\Log\LoggerAwareTrait',
        ], ClassUtils::getTraits(FirstService::class));
        $this->assertEquals([
            'RunOpenCode\Bundle\Traitor\Tests\Fixtures\Mocks\Traits\FirstTrait' => 'RunOpenCode\Bundle\Traitor\Tests\Fixtures\Mocks\Traits\FirstTrait',
            'Psr\Log\LoggerAwareTrait' => 'Psr\Log\LoggerAwareTrait',
            'RunOpenCode\Bundle\Traitor\Tests\Fixtures\Mocks\Traits\SecondTrait' => 'RunOpenCode\Bundle\Traitor\Tests\Fixtures\Mocks\Traits\SecondTrait',
        ], ClassUtils::getTraits(ThirdService::class));
    }

    /**
     * @test
     * @expectedException \RunOpenCode\Bundle\Traitor\Exception\InvalidArgumentException
     */
    public function getTraitsThrowsInvalidArgumentExceptionOnScalar()
    {
        ClassUtils::getTraits(10);
    }

    /**
     * @test
     * @expectedException \RunOpenCode\Bundle\Traitor\Exception\RuntimeException
     */
    public function getTraitsThrowsRuntimeExceptionOnNonExistingClass()
    {
        ClassUtils::getTraits('SomeNonExistingClass');
    }

    /**
     * @test
     */
    public function isWithinNamespace()
    {
        $first = new FirstService();
        $third = new ThirdService();

        $this->assertFalse(ClassUtils::isWithinNamespace($first, 'RunOpenCode\Bundle\Traitor\Tests\Fixtures\Mocks\Deeper'));
        $this->assertTrue(ClassUtils::isWithinNamespace($third, 'RunOpenCode\Bundle\Traitor\Tests\Fixtures\Mocks\Deeper'));
        $this->assertFalse(ClassUtils::isWithinNamespace(FirstService::class, 'RunOpenCode\Bundle\Traitor\Tests\Fixtures\Mocks\Deeper'));
        $this->assertTrue(ClassUtils::isWithinNamespace(ThirdService::class, 'RunOpenCode\Bundle\Traitor\Tests\Fixtures\Mocks\Deeper'));
    }

    /**
     * @test
     * @expectedException \RunOpenCode\Bundle\Traitor\Exception\InvalidArgumentException
     */
    public function isWithinNamespaceThrowsInvalidArgumentExceptionOnScalar()
    {
        ClassUtils::isWithinNamespace(10, 'RunOpenCode\Bundle\Traitor\Tests\Fixtures\Mocks\Deeper');
    }

    /**
     * @test
     * @expectedException \RunOpenCode\Bundle\Traitor\Exception\RuntimeException
     */
    public function isWithinNamespaceThrowsRuntimeExceptionOnNonExistingClass()
    {
        ClassUtils::isWithinNamespace('SomeNonExistingClass', 'RunOpenCode\Bundle\Traitor\Tests\Fixtures\Mocks\Deeper');
    }
}
