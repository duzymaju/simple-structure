<?php

namespace SimpleStructure;

use SimpleStructure\Container\Definition;
use SimpleStructure\Exception\BadClassCallException;
use SimpleStructure\Exception\BadDefinitionCallException;

/**
 * Container
 */
class Container
{
    /** @var Definition[] */
    private $definitions = [];

    /** @var mixed[] */
    private $items = [];

    /** @var Definition[] */
    private $pendingDefinitions = [];

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
        $this->definitions[$name] = new Definition($this, $classNameOrFactory, $dependencies, $params);

        return $this;
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
        if (array_key_exists($name, $this->items)) {
            return $this->items[$name];
        }
        $this->items[$name] = $this->create($name);

        if (count($this->pendingDefinitions) > 0 && $name === array_keys($this->pendingDefinitions)[0]) {
            foreach ($this->pendingDefinitions as $itemName => $pendingDefinition) {
                if (array_key_exists($itemName, $this->items)) {
                    $pendingDefinition->callMethods($this->items[$itemName]);
                }
            }
            $this->pendingDefinitions = [];
        }

        return $this->items[$name];
    }

    /**
     * Get definition
     *
     * @param string $name name
     *
     * @return Definition
     */
    public function getDefinition($name)
    {
        if (!array_key_exists($name, $this->definitions)) {
            throw new BadDefinitionCallException(sprintf('Definition "%s" doesn\'t exist.', $name));
        }

        return $this->definitions[$name];
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
        $definition = $this->getDefinition($name);
        $this->pendingDefinitions[$name] = $definition;

        return $definition->create($params);
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
}
