<?php

namespace SimpleStructure\Container;

use SimpleStructure\Container;

/**
 * Object definition
 */
class Definition
{
    /** @var Container */
    private $container;

    /** @var string|callable */
    private $classNameOrFactory;

    /** @var string[] */
    private $dependencies;

    /** @var array */
    private $params;

    /**
     * Construct
     *
     * @param Container       $container          container
     * @param string|callable $classNameOrFactory class name or factory
     * @param string[]        $dependencies       dependencies
     * @param array           $params             params
     */
    public function __construct(Container $container, $classNameOrFactory, array $dependencies = [], array $params = [])
    {
        $this->container = $container;
        $this->classNameOrFactory = $classNameOrFactory;
        $this->dependencies = $dependencies;
        $this->params = $params;
    }

    /**
     * Create
     *
     * @param array $params params
     *
     * @return mixed
     */
    public function create(array $params = [])
    {
        $classNameOrFactory = $this->classNameOrFactory;
        $dependencies = array_map(function ($typeName) {
            $parts = explode(':', $typeName);
            if (count($parts) < 2) {
                array_unshift($parts, '');
            }
            switch ($parts[0]) {
                case 'i': // instance
                    return $this->container->create($parts[1]);

                case 'd': // definition
                    return $this->container->getDefinition($parts[1]);

                default: // singleton
                    return $this->container->get($parts[1]);
            }
        }, $this->dependencies);
        $instance = is_callable($classNameOrFactory) ?
            $classNameOrFactory(...$dependencies, ...$this->params, ...$params) :
            new $classNameOrFactory(...$dependencies, ...$this->params, ...$params);

        return $instance;
    }
}
