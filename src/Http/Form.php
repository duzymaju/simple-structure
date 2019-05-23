<?php

namespace SimpleStructure\Http;

use ArrayObject;
use SimpleStructure\Model\FileSystem\File;
use stdClass;

/**
 * HTTP form
 */
class Form
{
    /** @var array */
    private $fields = [];

    /**
     * Add field
     *
     * @param string $name  name
     * @param mixed  $value value
     *
     * @return self
     */
    public function addField($name, $value)
    {
        $this->fields[$name] = $value;

        return $this;
    }

    /**
     * To post fields
     *
     * @return array
     */
    public function toPostFields()
    {
        $postFields = [];
        $this->generatePostFields($this->fields, '', $postFields);

        return $postFields;
    }

    /**
     * Generate post fields
     *
     * @param mixed  $data     data
     * @param string $keyPath  key path
     * @param array  $response response
     */
    private function generatePostFields($data, $keyPath, &$response)
    {
        if ($data instanceof stdClass || $data instanceof ArrayObject) {
            $data = (array) $data;
        }
        if (is_array($data)) {
            foreach ($data as $key => $value) {
                $keySubPath = empty($keyPath) ? $key : $keyPath . '[' . $key . ']';
                $this->generatePostFields($value, $keySubPath, $response);
            }
        } else {
            if ($data instanceof File) {
                $data = $data->getCurlFile();
            }
            $response[$keyPath] = $data;
        }
    }
}
