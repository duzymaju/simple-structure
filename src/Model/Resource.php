<?php

namespace SimpleStructure\Model;

use SimpleStructure\Http\Response;
use SimpleStructure\Model\FileSystem\FileContent;
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

    /** @var string */
    private $url;

    /**
     * Construct
     *
     * @param string $content    content
     * @param array  $headers    headers
     * @param int    $statusCode status code
     * @param string $url        URL
     */
    public function __construct($content, array $headers = [], $statusCode = Response::OK, $url = '')
    {
        $this->headers = new ParamPack($headers);
        $this->content = $content;
        $this->statusCode = $statusCode;
        $this->url = $url;
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
        switch ($format) {
            case 'json':
                return json_decode($this->content);

            case 'json-array':
                return json_decode($this->content, true);

            case 'file':
                return new FileContent($this->url, $this->content);

            default:
                return $this->content;
        }
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
     * Get file content
     *
     * @return FileContent
     */
    public function getFileContent()
    {
        return $this->getContent('file');
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
