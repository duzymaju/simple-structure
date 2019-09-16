<?php

namespace SimpleStructure;

use Exception;
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
     */
    public function execute()
    {
        /** @var bool $isDev */
        $isDev = $this->container->get('isDev');
        /** @var Response $response */
        $response = $this->container->get('response');
        try {
            /** @var Request $request */
            $request = $this->container->get('request');
            foreach ($this->actions as $action) {
                if (!$action->isRequested($request)) {
                    continue;
                }
                $action->execute($this->container, $response, $request);
                $response->send();
            }
            throw new NotFoundException('Page not found');
        } catch (Exception $exception) {
            $statusCode = $exception instanceof WebException ? $exception->getCode() : Response::INTERNAL_ERROR;
            $response->setStatusCode($statusCode);
            if ($isDev || $exception instanceof WebException) {
                if (isset($this->errorCallback)) {
                    $errorCallback = $this->errorCallback;
                    $errorCallback($this->container, $response, $request, $exception);
                } else {
                    $response->setContent([
                        'error' => $exception->getMessage(),
                    ]);
                }
            }
            $response->send();
        }
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
