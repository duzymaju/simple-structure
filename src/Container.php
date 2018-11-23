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

    /**
     * Set object
     *
     * @param string   $name         name
     * @param string   $className    class name
     * @param string[] $dependencies dependencies
     * @param array    $params       params
     *
     * @return self
     *
     * @throws BadClassCallException
     */
    public function setObject($name, $className, array $dependencies = [], array $params = [])
    {
        if (!class_exists($className)) {
            throw new BadClassCallException(sprintf('Class "%s" doesn\'t exist.', $className));
        }
        $this->definitions[$name] = new Definition($this, $className, $dependencies, $params);

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
        return $this
            ->getDefinition($name)
            ->create($params)
        ;
    }

    /**
     * Set
     *
     * @param string   $name         name
     * @param string   $className    class name
     * @param string[] $dependencies dependencies
     * @param array    $params       params
     *
     * @return self
     */
    public function set($name, $className, array $dependencies = [], array $params = [])
    {
        return $this->setObject($name, $className, $dependencies, $params);
    }
}
