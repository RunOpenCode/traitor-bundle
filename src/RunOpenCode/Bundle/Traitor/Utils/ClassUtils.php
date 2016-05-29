<?php
/*
 * This file is part of the  TraitorBundle, an RunOpenCode project.
 *
 * (c) 2016 RunOpenCode
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace RunOpenCode\Bundle\Traitor\Utils;

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
     * @throws \RuntimeException
     */
    public static function usesTrait($objectOrClass, $trait, $autoload = true)
    {
        if (is_object($objectOrClass)) {
            $objectOrClass = get_class($objectOrClass);
        }

        if (!is_string($objectOrClass)) {
            throw new \RuntimeException(sprintf('FQCN string expected, got: "%s".', gettype($objectOrClass)));
        }

        if (in_array(ltrim($trait, '\\'), self::getTraits($objectOrClass, $autoload), false)) {
            return true;
        }

        return false;
    }

    /**
     * Get ALL traits used by one class.
     *
     * @param string $class FQCN.
     * @param bool $autoload Weather to autoload class.
     *
     * @return array Used traits.
     */
    public static function getTraits($class, $autoload = true)
    {
        $traits = [];

        // Get traits of all parent classes
        do {
            $traits = array_merge(class_uses($class, $autoload), $traits);
        } while ($class = get_parent_class($class));

        // Get traits of all parent traits
        $traitsToSearch = $traits;

        while (count($traitsToSearch) > 0) {
            $newTraits = class_uses(array_pop($traitsToSearch), $autoload);
            $traits = array_merge($newTraits, $traits);
            $traitsToSearch = array_merge($newTraits, $traitsToSearch);
        };

        foreach ($traits as $trait => $same) {
            $traits = array_merge(class_uses($trait, $autoload), $traits);
        }

        return array_unique(array_map(function($fqcn) {
            return ltrim($fqcn, '\\');
        }, $traits));
    }

    /**
     * Check if class is within certain namespace.
     *
     * @param string|object $objectOrClass Class to check.
     * @param string $namespacePrefix Namespace prefix
     * @return bool
     */
    public static function isWithinNamespace($objectOrClass, $namespacePrefix)
    {
        if (is_object($objectOrClass)) {
            $objectOrClass = get_class($objectOrClass);
        }

        if (!is_string($objectOrClass)) {
            throw new \RuntimeException(sprintf('FQCN string expected, got: "%s".', gettype($objectOrClass)));
        }

        $objectOrClass = ltrim($objectOrClass, '\\');
        $namespacePrefix = rtrim(ltrim($namespacePrefix, '\\'), '\\') . '\\';

        return strpos($objectOrClass, $namespacePrefix) === 0;
    }


}
