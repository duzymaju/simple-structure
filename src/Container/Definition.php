<?php

namespace SimpleStructure\Container;

use SimpleStructure\Container;
use SimpleStructure\Exception\BadMethodCallException;
use stdClass;

/**
 * Object definition
 */
class Definition
{
    /** @var Container */
    private $container;

    /** @var string|callable */
    private $classNameOrFactory;

    /** @var stdClass[] */
    private $methodCalls = [];

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
     * Add method call
     *
     * @param string   $methodName   method name
     * @param string[] $dependencies dependencies
     * @param array    $params       params
     *
     * @return self
     */
    public function addMethodCall($methodName, array $dependencies = [], array $params = [])
    {
        $this->methodCalls[] = (object) [
            'dependencies' => $dependencies,
            'methodName' => $methodName,
            'params' => $params,
        ];

        return $this;
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
        $dependencies = $this->getDependencies($this->dependencies);

        return is_callable($classNameOrFactory) ?
            $classNameOrFactory(...$dependencies, ...$this->params, ...$params) :
            new $classNameOrFactory(...$dependencies, ...$this->params, ...$params);
    }

    /**
     * Call methods
     *
     * @param object $instance instance
     *
     * @return self
     */
    public function callMethods($instance)
    {
        foreach ($this->methodCalls as $methodCall) {
            $methodName = $methodCall->methodName;
            if (!method_exists($instance, $methodName)) {
                throw new BadMethodCallException(sprintf('There is no %s method to call.', $methodName));
            }
            $instance->$methodName(...$this->getDependencies($methodCall->dependencies), ...$methodCall->params);
        }

        return $this;
    }

    /**
     * Get dependencies
     *
     * @param string[] $dependencies dependencies
     *
     * @return mixed[]
     */
    private function getDependencies(array $dependencies)
    {
        return array_map(function ($typeName) {
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
        }, $dependencies);
    }
}
