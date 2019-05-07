<?php

namespace SimpleStructure\Model;

use SimpleStructure\Http\Response;
use SimpleStructure\Tool\ParamPack;

/**
 * Resource model
 */
class Resource
{
    /** @var ParamPack */
    public $headers;

    /** @var string */
    private $content;

    /** @var int */
    private $statusCode;

    /**
     * Construct
     *
     * @param string $content    content
     * @param array  $headers    headers
     * @param int    $statusCode status code
     */
    public function __construct($content, array $headers = [], $statusCode = Response::OK)
    {
        $this->headers = new ParamPack($headers);
        $this->content = $content;
        $this->statusCode = $statusCode;
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
        if ($format === 'json') {
            return json_decode($this->content);
        } elseif ($format === 'json-array') {
            return json_decode($this->content, true);
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
     * Get status code
     *
     * @return int
     */
    public function getStatusCode()
    {
        return $this->statusCode;
    }
}
