<?php

namespace SimpleStructure\Http;

use SimpleStructure\Exception\CookieException;
use SimpleStructure\Tool\Paginator;
use SimpleStructure\Tool\ParamPack;

/**
 * HTTP response
 */
class Response
{
    /** @const int */
    const OK = 200;

    /** @const int */
    const NO_CONTENT = 204;

    /** @const int */
    const MULTIPLE_CHOICES = 300;

    /** @const int */
    const MOVED_PERMANENTLY = 301;

    /** @const int */
    const FOUND = 302;

    /** @const int */
    const BAD_REQUEST = 400;

    /** @const int */
    const UNAUTHORIZED = 401;

    /** @const int */
    const FORBIDDEN = 403;

    /** @const int */
    const NOT_FOUND = 404;

    /** @const int */
    const INTERNAL_ERROR = 500;

    /** @const int */
    const UNAVAILABLE = 503;

    /** @var ParamPack */
    public $headers;

    /** @var ParamPack */
    private $cookies;

    /** @var int */
    private $statusCode = self::OK;

    /** @var mixed */
    private $content;

    private static $statusNames = [
        self::OK => 'OK',
        self::NO_CONTENT => 'No Content',
        self::MULTIPLE_CHOICES => 'Multiple Choices',
        self::MOVED_PERMANENTLY => 'Moved Permanently',
        self::FOUND => 'Found',
        self::BAD_REQUEST => 'Bad Request',
        self::UNAUTHORIZED => 'Unauthorized',
        self::FORBIDDEN => 'Forbidden',
        self::NOT_FOUND => 'Not Found',
        self::INTERNAL_ERROR => 'Internal Server Error',
        self::UNAVAILABLE => 'Service Unavailable',
    ];

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->headers = new ParamPack();
        $this->headers
            ->add('Cache-Control', 'no-cache, must-revalidate')
            ->add('Expires', gmdate('D, d M Y H:i:s T'))
        ;
        $this->cookies = new ParamPack();
    }

    /**
     * Set status code
     *
     * @param int $statusCode status code
     *
     * @return self
     */
    public function setStatusCode($statusCode)
    {
        $this->statusCode = $statusCode;

        return $this;
    }

    /**
     * Set content
     *
     * @param mixed $content content
     *
     * @return self
     */
    public function setContent($content)
    {
        $this->content = $content;

        return $this;
    }

    /**
     * Set cookie
     *
     * @param string $name     name
     * @param string $value    value
     * @param int    $expire   expire
     * @param string $path     path
     * @param string $domain   domain
     * @param bool   $secure   secure
     * @param bool   $httpOnly HTTP only
     *
     * @return self
     *
     * @throws CookieException
     */
    public function setCookie($name, $value, $expire = 0, $path = '/', $domain = '', $secure = false, $httpOnly = true)
    {
        if (empty($domain)) {
            $domain = $_SERVER['SERVER_NAME'];
        }
        if (!setcookie($name, $value, $expire, $path, $domain, $secure, $httpOnly)) {
            throw new CookieException('An exception occurred during cookie setting.');
        }
        if ($expire == 0 || $expire > time()) {
            $this->cookies->add($name, $value);
        }

        return $this;
    }

    /**
     * Redirect
     *
     * @param string $url       URL
     * @param bool   $permanent permanent
     */
    public function redirect($url, $permanent = false)
    {
        $this->headers->add('Location', $url);
        $this
            ->setStatusCode($permanent ? Response::MOVED_PERMANENTLY : Response::FOUND)
            ->send()
        ;
    }

    /**
     * Send
     */
    public function send()
    {
        if ($this->statusCode != Response::OK) {
            $statusName = sprintf('%d %s', $this->statusCode, $this->getStatusName($this->statusCode));
            $this->headers
                ->add('HTTP/1.1', $statusName)
                ->add('Status', $statusName)
            ;
        }
        if (is_array($this->content) || is_object($this->content)) {
            $this->headers->add('Content-Type', 'application/json');
            $content = $this->content instanceof Paginator && !isset($this->content->pack) ?
                json_encode($this->content->getArrayCopy()) : json_encode($this->content);
        } else {
            if (!$this->headers->has('Content-Type')) {
                $this->headers->add('Content-Type', 'text/plain');
            }
            $content = (string) $this->content;
        }
        foreach ($this->headers->getPack() as $key => $value) {
            if (isset($value)) {
                header($key . ': ' . $value);
            }
        }
        echo $content;
        exit;
    }

    /**
     * Get status name
     *
     * @param int $statusCode status code
     *
     * @return string
     */
    private function getStatusName($statusCode)
    {
        if (!array_key_exists($statusCode, self::$statusNames)) {
            return '';
        }

        return self::$statusNames[$statusCode];
    }
}
