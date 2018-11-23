<?php

namespace SimpleStructure\Model;

use SimpleStructure\Container;
use SimpleStructure\Http\Request;
use SimpleStructure\Http\Response;

/**
 * Action model
 */
class Action
{
    /** @var callable */
    private $callback;

    /** @var string */
    private $method;

    /** @var string */
    private $path;

    /** @var array */
    private $variables = [];

    /**
     * Construct
     *
     * @param string   $method   method
     * @param string   $path     path
     * @param callable $callback callback
     */
    public function __construct($method, $path, callable $callback)
    {
        $this->callback = $callback;
        $this->method = $method;
        $this->path = rtrim($path, '/');
    }

    /**
     * Is requested
     *
     * @param Request $request request
     *
     * @return bool
     */
    public function isRequested(Request $request)
    {
        $this->variables = [];

        if (strtolower($this->method) !== $request->getMethod()) {
            return false;
        }

        $requestPath = explode('/', $request->getPath());
        $actionPath = explode('/', $this->path);
        if (count($requestPath) !== count($actionPath)) {
            return false;
        }

        foreach ($actionPath as $index => $element) {
            $length = mb_strlen($element);
            if ($element[0] === '{' && $element[$length - 1] === '}') {
                $nameParts = explode(':', mb_substr($element, 1, $length - 2));
                $name = $nameParts[0];
                $value = $this->mapValue($requestPath[$index], count($nameParts) > 1 ? $nameParts[1] : null);
                if ((string) $requestPath[$index] !== (string) $value) {
                    return false;
                }
                $this->variables[$name] = $value;
            } elseif ($requestPath[$index] !== $element) {
                return false;
            }
        }

        return true;
    }

    /**
     * Execute
     *
     * @param Container $container container
     * @param Response  $response  response
     * @param Request   $request   request
     */
    public function execute(Container $container, Response $response, Request $request)
    {
        $callback = $this->callback;
        $variables = $this->variables;
        $this->variables = [];
        $callback($container, $response, $request, ...array_values($variables));
    }

    /**
     * Map value
     *
     * @param mixed       $value value
     * @param string|null $type  type
     *
     * @return float|int|string
     */
    private function mapValue($value, $type = null)
    {
        switch ($type) {
            case 'int':
                return (int) $value;

            case 'float':
                return (float) $value;

            default:
                return (string) $value;
        }
    }
}
