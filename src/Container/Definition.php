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

    /** @var string */
    private $className;

    /** @var string[] */
    private $dependencies;

    /** @var array */
    private $params;

    /**
     * Construct
     *
     * @param Container $container          container
     * @param string    $className          class name
     * @param string[]  $dependencies       dependencies
     * @param array     $params             params
     */
    public function __construct(Container $container, $className, array $dependencies = [], array $params = [])
    {
        $this->container = $container;
        $this->className = $className;
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
        $className = $this->className;
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

        return new $className(...$dependencies, ...$this->params, ...$params);
    }
}
