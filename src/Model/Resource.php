<?php

namespace SimpleStructure\Model;

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

    /**
     * Construct
     *
     * @param string $content content
     * @param array  $headers headers
     */
    public function __construct($content, array $headers = [])
    {
        $this->headers = new ParamPack($headers);
        $this->content = $content;
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
}
