<?php

namespace SimpleStructure\Tool;

use SimpleStructure\Model\FileSystem\File;

/**
 * Files param pack tool
 */
class FilesParamPack extends ParamPack
{
    /**
     * Constructor
     *
     * @param array $params params
     */
    public function __construct(array $params = [])
    {
        foreach ($params as $key => $value) {
            $params[$key] = is_array($value) ? $this->normalizeFiles($value) : null;
        }
        parent::__construct($params);
    }

    /**
     * Normalize files
     *
     * @param array $params params
     *
     * @return array|null
     */
    private function normalizeFiles(array $params)
    {
        if (is_array($params['error'])) {
            $files = [];
            foreach (array_keys($params['error']) as $i) {
                if (!empty($params['tmp_name'][$i]) || $params['size'][$i] != 0) {
                    $files[] = File::create($params['tmp_name'][$i], $params['name'][$i], $params['size'][$i],
                        $params['error'][$i], true);
                }
            }
        } else {
            $files = !empty($params['tmp_name']) || $params['size'] != 0 ?
                File::create($params['tmp_name'], $params['name'], $params['size'], $params['error'], true) :
                null;
        }

        return $files;
    }
}
