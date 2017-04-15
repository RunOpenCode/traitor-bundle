<?php
/*
 * This file is part of the  TraitorBundle, an RunOpenCode project.
 *
 * (c) 2017 RunOpenCode
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace RunOpenCode\Bundle\Traitor\Utils;

use RunOpenCode\Bundle\Traitor\Exception\InvalidArgumentException;
use RunOpenCode\Bundle\Traitor\Exception\RuntimeException;

final class ClassUtils
{
    final private function __construct() { /** noop */ }

    /**
     * Check if class uses trait.
     *
     * @param object|string $objectOrClass Instance of class or FQCN
     * @param string $trait FQCN trait
     * @param bool $autoload Weather to autoload class.
     *
     * @return bool TRUE if class uses trait.
     *
     * @throws \RunOpenCode\Bundle\Traitor\Exception\InvalidArgumentException
     * @throws \RunOpenCode\Bundle\Traitor\Exception\RuntimeException
     */
    public static function usesTrait($objectOrClass, $trait, $autoload = true)
    {
        if (is_object($objectOrClass)) {
            $objectOrClass = get_class($objectOrClass);
        }

        if (!is_string($objectOrClass)) {
            throw new InvalidArgumentException(sprintf('Full qualified class name expected, got: "%s".', gettype($objectOrClass)));
        }

        if (!class_exists($objectOrClass)) {
            throw new RuntimeException(sprintf('Class "%s" does not exists or it can not be autoloaded.', $objectOrClass));
        }

        if (in_array(ltrim($trait, '\\'), self::getTraits($objectOrClass, $autoload), false)) {
            return true;
        }

        return false;
    }

    /**
     * Get ALL traits used by one class.
     *
     * @param object|string $objectOrClass Instance of class or FQCN
     * @param bool $autoload Weather to autoload class.
     *
     * @throws \RunOpenCode\Bundle\Traitor\Exception\InvalidArgumentException
     * @throws \RunOpenCode\Bundle\Traitor\Exception\RuntimeException
     *
     * @return array Used traits.
     */
    public static function getTraits($objectOrClass, $autoload = true)
    {
        if (is_object($objectOrClass)) {
            $objectOrClass = get_class($objectOrClass);
        }

        if (!is_string($objectOrClass)) {
            throw new InvalidArgumentException(sprintf('Full qualified class name expected, got: "%s".', gettype($objectOrClass)));
        }

        if (!class_exists($objectOrClass)) {
            throw new RuntimeException(sprintf('Class "%s" does not exists or it can not be autoloaded.', $objectOrClass));
        }

        $traits = [];

        // Get traits of all parent classes
        do {
            $traits = array_merge(class_uses($objectOrClass, $autoload), $traits);
        } while ($objectOrClass = get_parent_class($objectOrClass));

        // Get traits of all parent traits
        $traitsToSearch = $traits;

        while (count($traitsToSearch) > 0) {
            $newTraits = class_uses(array_pop($traitsToSearch), $autoload);
            $traits = array_merge($newTraits, $traits);
            $traitsToSearch = array_merge($newTraits, $traitsToSearch);
        }

        foreach ($traits as $trait => $same) {
            $traits = array_merge(class_uses($trait, $autoload), $traits);
        }

        return array_unique(array_map(function ($fqcn) {
            return ltrim($fqcn, '\\');
        }, $traits));
    }

    /**
     * Check if class is within certain namespace.
     *
     * @param string|object $objectOrClass Class to check.
     * @param string $namespacePrefix Namespace prefix
     *
     * @return bool
     *
     * @throws \RunOpenCode\Bundle\Traitor\Exception\InvalidArgumentException
     * @throws \RunOpenCode\Bundle\Traitor\Exception\RuntimeException
     */
    public static function isWithinNamespace($objectOrClass, $namespacePrefix)
    {
        if (is_object($objectOrClass)) {
            $objectOrClass = get_class($objectOrClass);
        }

        if (!is_string($objectOrClass)) {
            throw new InvalidArgumentException(sprintf('Full qualified class name string expected, got: "%s".', gettype($objectOrClass)));
        }

        if (!class_exists($objectOrClass)) {
            throw new RuntimeException(sprintf('Class "%s" does not exists or it can not be autoloaded.', $objectOrClass));
        }

        $objectOrClass = ltrim($objectOrClass, '\\');
        $namespacePrefix = rtrim(ltrim($namespacePrefix, '\\'), '\\').'\\';

        return strpos($objectOrClass, $namespacePrefix) === 0;
    }
}
