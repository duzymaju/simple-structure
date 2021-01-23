<?php

namespace SimpleStructure\Container;

use stdClass;

/**
 * Object definition
 */
class Definition
{
    /** @var string|callable */
    public $classNameOrFactory;

    /** @var string[] */
    public $dependencies;

    /** @var array */
    public $params;

    /** @var stdClass[] */
    public $methodCalls = [];

    /**
     * Construct
     *
     * @param string|callable $classNameOrFactory class name or factory
     * @param string[]        $dependencies       dependencies
     * @param array           $params             params
     */
    public function __construct($classNameOrFactory, array $dependencies = [], array $params = [])
    {
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
}
