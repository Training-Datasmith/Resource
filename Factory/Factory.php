<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Sylius\Component\Resource\Factory;

/**
 * Creates new resource instances by their fully-qualified class name.
 *
 * Instantiates the registered class with no constructor arguments. This is
 * the standard factory for simple domain objects that have no required
 * dependencies. For resources requiring constructor injection, implement
 * FactoryInterface directly.
 */
final class Factory implements FactoryInterface
{
    /**
     * @param string $className Fully-qualified class name of the resource to create
     *
     * @psalm-param class-string $className
     */
    public function __construct(
        /**
         * @psalm-var class-string
         */
        private string $className,
    ) {
    }

    /**
     * Creates and returns a new instance of the registered resource class.
     *
     * The object is created with no constructor arguments (new $class()). If
     * the class requires constructor parameters, use a custom factory instead.
     *
     * @return object A new instance of the configured resource class
     *
     * @complexity O(1)
     */
    public function createNew(): object
    {
        return new $this->className();
    }
}
