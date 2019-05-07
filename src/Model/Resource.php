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
    private $status;

    /**
     * Construct
     *
     * @param string $content content
     * @param array  $headers headers
     * @param int    $status  status
     */
    public function __construct($content, array $headers = [], $status = Response::OK)
    {
        $this->headers = new ParamPack($headers);
        $this->content = $content;
        $this->status = $status;
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
     * Get status
     *
     * @return int
     */
    public function getStatus()
    {
        return $this->status;
    }
}
