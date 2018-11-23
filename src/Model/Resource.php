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
    public $content;

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
}
