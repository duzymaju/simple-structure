<?php

namespace SimpleStructure;

use Exception;
use SimpleStructure\Exception\InternalException;
use SimpleStructure\Exception\NotFoundException;
use SimpleStructure\Exception\WebException;
use SimpleStructure\Http\Request;
use SimpleStructure\Http\Response;
use SimpleStructure\Model\Action;

/**
 * Bootstrap
 */
class Bootstrap
{
    /** @var Container */
    private $container;

    /** @var bool */
    private $isRequest;

    /** @var Action[] */
    private $actions = [];

    /** @var callable|null */
    private $allOptionsCallback;

    /** @var callable|null */
    private $errorCallback;

    /**
     * Construct
     *
     * @param string $baseDir base dir
     */
    public function __construct($baseDir)
    {
        $this->container = new Container();
        $this->container->setParam('baseDir', $baseDir);
        if ($this->isRequest()) {
            $this->container
                ->setObject('action', Action::class)
                ->setObject('request', Request::class)
                ->setObject('response', Response::class)
            ;
        }
    }

    /**
     * Get container
     *
     * @return Container
     */
    public function getContainer()
    {
        return $this->container;
    }

    /**
     * Define
     *
     * @param callable $callback callback
     *
     * @return self
     */
    public function define(callable $callback)
    {
        $callback($this->container);

        return $this;
    }

    /**
     * Define on request
     *
     * @param callable $callback callback
     *
     * @return self
     */
    public function defineOnRequest(callable $callback)
    {
        if ($this->isRequest()) {
            $callback($this->container);
        }

        return $this;
    }

    /**
     * Define action
     *
     * @param string   $method   method
     * @param string   $path     path
     * @param callable $callback callback
     *
     * @return self
     */
    public function defineAction($method, $path, callable $callback)
    {
        $this->actions[] = $this->container->create('action', [
            $method, $path, $callback,
        ]);

        return $this;
    }

    /**
     * Define all options action
     *
     * @param callable $callback callback
     *
     * @return self
     */
    public function defineAllOptionsActions(callable $callback)
    {
        $this->allOptionsCallback = $callback;

        return $this;
    }

    /**
     * Define errors
     *
     * @param callable $errorCallback error callback
     *
     * @return self
     */
    public function defineErrors(callable $errorCallback)
    {
        $this->errorCallback = $errorCallback;

        return $this;
    }

    /**
     * Execute
     *
     * @return void
     */
    public function execute()
    {
        try {
            $response = $this->getOptionsResponseIfExists();
            if (!isset($response)) {
                $response = $this->getActionResponseIfExists();
            }
            if (!isset($response)) {
                throw new NotFoundException();
            }
        } catch (Exception $exception) {
            /** @var bool $isDev */
            $isDev = $this->container->get('isDev');
            /** @var Response $response */
            $response = $this->container->get('response');
            if (!($exception instanceof WebException)) {
                $message = $isDev && !empty($exception->getMessage()) ? $exception->getMessage() : null;
                $exception = new InternalException($message, $exception);
            }
            $response->setStatusCode($exception->getCode());
            if (isset($this->errorCallback)) {
                $errorCallback = $this->errorCallback;
                $errorCallback($this->container, $response, $this->container->get('request'), $exception);
            } elseif ($isDev) {
                $response->setContent([
                    'error' => $exception->getMessage(),
                ]);
            }
        }
        $response->send();
    }

    /**
     * Get action response if exists
     *
     * @return Response|null
     */
    private function getActionResponseIfExists()
    {
        /** @var Request $request */
        $request = $this->container->get('request');
        foreach ($this->actions as $action) {
            if ($action->hasMatchedMethod($request) && $action->hasMatchedPath($request)) {
                /** @var Response $response */
                $response = $this->container->get('response');
                // Executes found action.
                $action->execute($this->container, $response, $request);
                return $response;
            }
        }

        return null;
    }

    /**
     * Get options response if exists
     *
     * @return Response|null
     */
    private function getOptionsResponseIfExists()
    {
        /** @var Request $request */
        $request = $this->container->get('request');
        if (!$request->isOptions() || !isset($this->allOptionsCallback)) {
            return null;
        }

        $matchedActions = [];
        foreach ($this->actions as $action) {
            if ($action->hasMatchedPath($request)) {
                if ($action->hasMatchedMethod($request)) {
                    /** @var Response $response */
                    $response = $this->container->get('response');
                    // Executes found OPTIONS action instead of callback.
                    $action->execute($this->container, $response, $request);
                    return $response;
                }
                $matchedActions[] = $action;
            }
        }

        if (count($matchedActions) > 0) {
            /** @var Response $response */
            $response = $this->container->get('response');
            $callback = $this->allOptionsCallback;
            // Executes OPTIONS callback.
            $callback($this->container, $response, $request, $matchedActions);
            return $response;
        }

        return null;
    }

    /**
     * Is request
     *
     * @return bool
     */
    private function isRequest()
    {
        if (isset($this->isRequest)) {
            return $this->isRequest;
        }
        $this->isRequest = !empty($_SERVER['REQUEST_METHOD']) && !empty($_SERVER['SERVER_NAME']) &&
            !empty($_SERVER['REQUEST_URI']);

        return $this->isRequest;
    }
}
