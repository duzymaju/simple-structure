<?php

namespace SimpleStructure;

use SimpleStructure\Container\Definition;
use SimpleStructure\Exception\BadClassCallException;
use SimpleStructure\Exception\BadDefinitionCallException;
use SimpleStructure\Exception\BadMethodCallException;

/**
 * Container
 */
class Container
{
    /** @var Definition[] */
    private $definitions = [];

    /** @var mixed[] */
    private $items = [];

    /**
     * Set object
     *
     * @param string          $name               name
     * @param string|callable $classNameOrFactory class name or factory
     * @param string[]        $dependencies       dependencies
     * @param array           $params             params
     *
     * @return self
     *
     * @throws BadClassCallException
     */
    public function setObject($name, $classNameOrFactory, array $dependencies = [], array $params = [])
    {
        if (!is_callable($classNameOrFactory) && !class_exists($classNameOrFactory)) {
            throw new BadClassCallException(sprintf('Class "%s" doesn\'t exist.', $classNameOrFactory));
        }
        $this->definitions[$name] = new Definition($classNameOrFactory, $dependencies, $params);

        return $this;
    }

    /**
     * Set
     *
     * @param string          $name               name
     * @param string|callable $classNameOrFactory class name or factory
     * @param string[]        $dependencies       dependencies
     * @param array           $params             params
     *
     * @return self
     */
    public function set($name, $classNameOrFactory, array $dependencies = [], array $params = [])
    {
        return $this->setObject($name, $classNameOrFactory, $dependencies, $params);
    }

    /**
     * Add object method call
     *
     * @param string   $name         name
     * @param string   $methodName   method name
     * @param string[] $dependencies dependencies
     * @param array    $params       params
     *
     * @return self
     *
     * @throws BadDefinitionCallException
     */
    public function addObjectMethodCall($name, $methodName, array $dependencies = [], array $params = [])
    {
        $this
            ->getDefinition($name)
            ->addMethodCall($methodName, $dependencies, $params)
        ;

        return $this;
    }

    /**
     * Set param
     *
     * @param string $name  name
     * @param mixed  $value value
     *
     * @return self
     */
    public function setParam($name, $value)
    {
        $this->items[$name] = $value;

        return $this;
    }

    /**
     * Get
     *
     * @param string $name name
     *
     * @return mixed
     */
    public function get($name)
    {
        $pendingDefinitions = [];
        $dependency = $this->getDependency($name, $pendingDefinitions);
        $this->callMethods($pendingDefinitions);

        return $dependency;
    }

    /**
     * Create
     *
     * @param string $name   name
     * @param array  $params params
     *
     * @return mixed
     */
    public function create($name, array $params = [])
    {
        $pendingDefinitions = [];
        $dependency = $this->createDependency($name, $pendingDefinitions, $params);
        $this->callMethods($pendingDefinitions);

        return $dependency;
    }

    /**
     * Get dependency
     *
     * @param string       $name                name
     * @param Definition[] &$pendingDefinitions pending definitions
     *
     * @return mixed
     */
    private function getDependency($name, array &$pendingDefinitions)
    {
        if (array_key_exists($name, $this->items)) {
            return $this->items[$name];
        }
        $this->items[$name] = $this->createDependency($name, $pendingDefinitions);

        return $this->items[$name];
    }

    /**
     * Create dependency
     *
     * @param string       $name                name
     * @param Definition[] &$pendingDefinitions pending definitions
     * @param array        $params              params
     *
     * @return mixed
     */
    private function createDependency($name, array &$pendingDefinitions, array $params = [])
    {
        $definition = $this->getDefinition($name);
        $pendingDefinitions[$name] = $definition;

        $classNameOrFactory = $definition->classNameOrFactory;
        $dependencies = $this->getDependencies($definition->dependencies, $pendingDefinitions);

        return is_callable($classNameOrFactory) ?
            $classNameOrFactory(...$dependencies, ...$definition->params, ...$params) :
            new $classNameOrFactory(...$dependencies, ...$definition->params, ...$params);
    }

    /**
     * Get dependencies
     *
     * @param string[]     $dependencies        dependencies
     * @param Definition[] &$pendingDefinitions pending definitions
     *
     * @return mixed[]
     */
    private function getDependencies(array $dependencies, array &$pendingDefinitions = [])
    {
        return array_map(function ($typeName) use (&$pendingDefinitions) {
            $parts = explode(':', $typeName);
            if (count($parts) < 2) {
                array_unshift($parts, '');
            }
            switch ($parts[0]) {
                case 'i': // instance
                    return $this->createDependency($parts[1], $pendingDefinitions);

                case 'd': // definition
                    return $this->getDefinition($parts[1]);

                default: // singleton
                    return $this->getDependency($parts[1], $pendingDefinitions);
            }
        }, $dependencies);
    }

    /**
     * Call methods
     *
     * @param Definition[] &$pendingDefinitions pending definitions
     *
     * @return self
     */
    private function callMethods(&$pendingDefinitions)
    {
        foreach ($pendingDefinitions as $itemName => $pendingDefinition) {
            if (array_key_exists($itemName, $this->items)) {
                $item = $this->items[$itemName];
                foreach ($pendingDefinition->methodCalls as $methodCall) {
                    $methodName = $methodCall->methodName;
                    if (!method_exists($item, $methodName)) {
                        throw new BadMethodCallException(sprintf('There is no %s method to call.', $methodName));
                    }
                    $item->$methodName(
                        ...$this->getDependencies($methodCall->dependencies), ...$methodCall->params
                    );
                }
            }
        }

        return $this;
    }

    /**
     * Get definition
     *
     * @param string $name name
     *
     * @return Definition
     */
    private function getDefinition($name)
    {
        if (!array_key_exists($name, $this->definitions)) {
            throw new BadDefinitionCallException(sprintf('Definition "%s" doesn\'t exist.', $name));
        }

        return $this->definitions[$name];
    }
}
