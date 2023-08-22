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
        $this->method = strtolower($method);
        $this->path = rtrim($path, '/');
    }

    /**
     * Get method
     *
     * @return string
     */
    public function getMethod()
    {
        return $this->method;
    }

    /**
     * Has matched method
     *
     * @param Request $request request
     *
     * @return bool
     */
    public function hasMatchedMethod(Request $request)
    {
        return $this->method === $request->getMethod();
    }

    /**
     * Has matched path
     *
     * @param Request $request request
     *
     * @return bool
     */
    public function hasMatchedPath(Request $request)
    {
        $this->variables = [];

        $requestPath = explode('/', $request->getPath());
        $actionPath = explode('/', $this->path);
        if (count($requestPath) !== count($actionPath)) {
            return false;
        }

        foreach ($actionPath as $index => $element) {
            $length = mb_strlen($element);
            $firstChar = mb_substr($element, 0, 1);
            $lastChar = mb_substr($element, -1, 1);
            if ($length > 2 && $firstChar === '{' && $lastChar === '}') {
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
