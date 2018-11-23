<?php

namespace SimpleStructure\Http;

use SimpleStructure\Tool\FilesParamPack;
use SimpleStructure\Tool\ParamPack;

/**
 * HTTP request
 */
class Request
{
    /** @const string */
    const DELETE = 'delete';

    /** @const string */
    const GET = 'get';

    /** @const string */
    const OPTIONS = 'options';

    /** @const string */
    const PATCH = 'patch';

    /** @const string */
    const POST = 'post';

    /** @const string */
    const PUT = 'put';

    /** @const string[] */
    const METHODS = [
        self::DELETE,
        self::GET,
        self::OPTIONS,
        self::PATCH,
        self::POST,
        self::PUT,
    ];

    /** @const string */
    const HTTP = 'http';

    /** @const string */
    const HTTPS = 'https';

    /** @const string[] */
    const PROTOCOLS = [
        self::HTTP,
        self::HTTPS,
    ];

    /** @const string */
    const SESSION_INDEX = 'SID';

    /** @var ParamPack */
    public $headers;

    /** @var ParamPack */
    public $query;

    /** @var ParamPack */
    public $request;

    /** @var ParamPack */
    public $params;

    /** @var ParamPack */
    public $files;

    /** @var ParamPack */
    public $cookies;

    /** @var string */
    private $method;

    /** @var string */
    private $protocol;

    /** @var string */
    private $domain;

    /** @var string */
    private $path;

    /** @var string */
    private $ip = '';

    /** @var mixed */
    private $content;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->headers = new ParamPack(array_change_key_case(getallheaders()));
        $this->query = new ParamPack($_GET);
        $this->request = new ParamPack($_POST);
        $this->files = new FilesParamPack($_FILES);
        $this->cookies = new ParamPack($_COOKIE);

        $this->params = new ParamPack();
        $this->params->addParentPack($this->request)
            ->addParentPack($this->query);
        if ($this->cookies->has(self::SESSION_INDEX)) {
            $_COOKIE[self::SESSION_INDEX] = $this->cookies->get(self::SESSION_INDEX);
        }

        $method = strtolower($_SERVER['REQUEST_METHOD']);
        $this->method = in_array($method, self::METHODS) ? $method : self::GET;
        $this->protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' ? self::HTTPS :
            self::HTTP;
        $this->domain = $_SERVER['SERVER_NAME'];
        $this->path = rtrim(strtok($_SERVER['REQUEST_URI'], '?'), '/');
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
     * Is DELETE
     *
     * @return bool
     */
    public function isDelete()
    {
        return $this->method == self::DELETE;
    }

    /**
     * Is GET
     *
     * @return bool
     */
    public function isGet()
    {
        return $this->method == self::GET;
    }

    /**
     * Is OPTIONS
     *
     * @return bool
     */
    public function isOptions()
    {
        return $this->method == self::OPTIONS;
    }

    /**
     * Is PATCH
     *
     * @return bool
     */
    public function isPatch()
    {
        return $this->method == self::PATCH;
    }

    /**
     * Is POST
     *
     * @return bool
     */
    public function isPost()
    {
        return $this->method == self::POST;
    }

    /**
     * Is PUT
     *
     * @return bool
     */
    public function isPut()
    {
        return $this->method == self::PUT;
    }

    /**
     * Is valid protocol
     *
     * @param string $protocol protocol
     *
     * @return bool
     */
    public function isValidProtocol($protocol)
    {
        return in_array($protocol, [
            self::HTTP,
            self::HTTPS,
        ]);
    }

    /**
     * Get protocol
     *
     * @return string
     */
    public function getProtocol()
    {
        return $this->protocol;
    }

    /**
     * Get domain
     *
     * @return string
     */
    public function getDomain()
    {
        return $this->domain;
    }

    /**
     * Get page address
     *
     * @param string|null $protocol protocol
     *
     * @return string
     */
    public function getPageAddress($protocol = null)
    {
        return ($this->isValidProtocol($protocol) ? $protocol : $this->protocol) . '://' . $this->getDomain();
    }

    /**
     * Get URL
     *
     * @param string|null $path         path
     * @param array       $params       params
     * @param bool|string $absolutePath absolute path
     * @param bool        $escapeUrl    escape URL
     *
     * @return string
     */
    public function getUrl($path = '/', array $params = [], $absolutePath = false, $escapeUrl = true)
    {
        if (!isset($path)) {
            $path = '';
        } elseif (!is_string($path) || empty($path)) {
            $path = '/';
        }
        if ($absolutePath) {
            $path = ($absolutePath === true ? $this->getPageAddress() : ($this->isValidProtocol($absolutePath) ?
                $this->getPageAddress($absolutePath) : $absolutePath)) . $path;
        }
        $url = $path . $this->getQueryString($params, $escapeUrl ? '&amp;' : '&');

        return $url;
    }

    /**
     * Get query string
     * 
     * @param array  $params      params
     * @param string $paramsJoint params joint
     * @param string $prefix      prefix
     *
     * @return string
     */
    private function getQueryString(array $params, $paramsJoint = '&amp;', $prefix = '?')
    {
        foreach ($params as $key => $param) {
            if (isset($param) && !is_object($param)) {
                $params[$key] = $this->urlEncodeParam($key, $param, $paramsJoint);
            } else {
                unset($params[$key]);
            }
        }
        $queryString = count($params) > 0 ? $prefix . implode($paramsJoint, $params) : '';

        return $queryString;
    }

    /**
     * URL encode param
     *
     * @param string $key               key
     * @param mixed  $param             param
     * @param string $paramsJoint       params joint
     * @param string $keyWithValueJoint key with value joint
     * @param int    $level             level
     *
     * @return string
     */
    private function urlEncodeParam($key, $param, $paramsJoint = '&amp;', $keyWithValueJoint = '=', $level = 1)
    {
        if ($level == 1) {
            $key = urlencode($key);
        }
        if (is_array($param)) {
            foreach ($param as $subKey => $subParam) {
                $param[$subKey] = $this->urlEncodeParam($key . '[' . urlencode($subKey) . ']', $subParam,
                    $paramsJoint, $keyWithValueJoint, $level + 1);
            }
            $encodedParam = implode($paramsJoint, $param);
        } else {
            $encodedParam = $key . $keyWithValueJoint . urlencode($param);
        }

        return $encodedParam;
    }

    /**
     * Get current URL
     *
     * @param array       $paramsToAdd   params to add
     * @param array|null  $namesToRemove names to remove
     * @param bool|string $absolutePath  absolute path
     *
     * @return string
     */
    public function getCurrentUrl(array $paramsToAdd = [], array $namesToRemove = null, $absolutePath = false)
    {
        $params = $namesToRemove === true ? $paramsToAdd : array_merge($this->query->getPack(), $paramsToAdd);
        if (is_array($namesToRemove)) {
            foreach ($namesToRemove as $nameToRemove) {
                if (array_key_exists($nameToRemove, $params)) {
                    unset($params[$nameToRemove]);
                }
            }
        }

        return $this->getUrl($this->path, $params, $absolutePath);
    }

    /**
     * Get current URL with only
     *
     * @param array       $namesToKeep  names to keep
     * @param array       $paramsToAdd  params to add
     * @param bool|string $absolutePath absolute path
     * 
     * @return string
     */
    public function getCurrentUrlWithOnly(array $namesToKeep, array $paramsToAdd = [], $absolutePath = false)
    {
        $params = [];
        foreach ($namesToKeep as $nameToKeep) {
            $param = $this->query->get($nameToKeep);
            if (isset($param)) {
                $params[$nameToKeep] = $param;
            }
        }

        return $this->getUrl($this->path, array_merge($params, $paramsToAdd), $absolutePath);
    }

    /**
     * Get URL with current params
     *
     * @param string      $path            path
     * @param array       $params          params
     * @param array       $paramNames      param names
     * @param bool        $overwriteParams overwrite params
     * @param bool|string $absolutePath    absolute path
     *
     * @return string
     */
    public function getUrlWithCurrentParams($path = '/', array $params = [], array $paramNames = [],
        $overwriteParams = true, $absolutePath = false)
    {
        foreach ($paramNames as $paramName) {
            $param = $this->query->get($paramName);
            if (isset($param) && ($overwriteParams || !isset($params[$paramName]))) {
                $params[$paramName] = $param;
            }
        }

        return $this->getUrl($path, $params, $absolutePath);
    }

    /**
     * Get content
     *
     * @param string $format format
     *
     * @return mixed
     */
    public function getContent($format = 'string')
    {
        if (!$this->content) {
            $this->content = file_get_contents('php://input') ?: null;
            if ($format == 'json') {
                $this->content = json_decode($this->content);
            } elseif ($format == 'json-array') {
                $this->content = json_decode($this->content, true);
            }
        }
        return $this->content;
    }

    /**
     * Get content from JSON
     *
     * @return mixed
     */
    public function getContentFromJson()
    {
        return $this->getContent('json');
    }

    /**
     * Get content params
     *
     * @return ParamPack
     */
    public function getContentParams()
    {
        return new ParamPack($this->getContent('json-array'));
    }

    /**
     * Get path
     *
     * @return string
     */
    public function getPath()
    {
        return $this->path;
    }

    /**
     * Get IP
     *
     * @return string|null
     */
    public function getIp()
    {
        if ($this->ip == '') {
            if (isset($_SERVER['HTTP_CLIENT_IP']) && !empty($_SERVER['HTTP_CLIENT_IP'])) {
                $this->ip = $_SERVER['HTTP_CLIENT_IP'];
            } elseif (isset($_SERVER['HTTP_X_FORWARDED_FOR']) && !empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
                $this->ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
            } elseif (isset($_SERVER['REMOTE_ADDR']) && !empty($_SERVER['REMOTE_ADDR'])) {
                $this->ip = $_SERVER['REMOTE_ADDR'];
            } else {
                $this->ip = null;
            }
        }

        return $this->ip;
    }

    /**
     * Get max file size
     *
     * @param int|null $maxFileSize max file size
     *
     * @return int
     */
    public function getMaxFileSize($maxFileSize = null)
    {
        $iniMaxFileSize = min($this->getIniInBytes('upload_max_filesize'),
            $this->getIniInBytes('post_max_size'));
        
        return isset($maxFileSize) ? (int) min($maxFileSize, $iniMaxFileSize) : $iniMaxFileSize;
    }

    /**
     * Get ini in bytes
     *
     * @param string $name name
     *
     * @return int
     */
    private function getIniInBytes($name)
    {
        $value = trim(ini_get($name));
        switch (strtolower($value[strlen($value) - 1])) {
            case 'g':
                $value *= pow(1024, 3);
                break;

            case 'm':
                $value *= pow(1024, 2);
                break;

            case 'k':
                $value *= pow(1024, 1);
                break;
        }

        return $value;
    }

    /**
     * Is post max size exceeded
     *
     * @return bool
     */
    public function isPostMaxSizeExceeded()
    {
        return $this->getMethod() != self::GET && empty($this->request) && empty($this->files) &&
            $_SERVER['CONTENT_LENGTH'] > 0 || false;
    }

    /**
     * Is ajax
     *
     * @return bool
     */
    public function isAjax()
    {
        $requestedWith = array_key_exists('HTTP_X_REQUESTED_WITH', $_SERVER) ? $_SERVER['HTTP_X_REQUESTED_WITH'] :
            null;

        return !empty($requestedWith) && strtolower($requestedWith) == 'xmlhttprequest';
    }
}
