<?php

namespace SimpleStructure\Model\FileSystem;

use stdClass;

/**
 * File abstract model
 */
abstract class FileAbstract
{
    /**
     * Get destination
     *
     * @param string $dirPath      dir path
     * @param string $fileName     file name
     * @param string $extension    extension
     * @param bool   $generateDirs generate directories
     *
     * @return stdClass
     */
    protected function getDestination($dirPath, $fileName, $extension, $generateDirs = false)
    {
        $destination = new stdClass();
        $destination->fileName = $fileName;
        $destination->extension = $this->unifyExtension($extension);
        $dirPath = rtrim(str_replace('\\', '/', $dirPath), '/');
        $destination->path = $dirPath . '/' . $destination->fileName . '.' . $destination->extension;
        if ($generateDirs && !is_dir($dirPath)) {
            mkdir($dirPath, 0777, true);
        }

        return $destination;
    }

    /**
     * Unify extension
     *
     * @param string $extension extension
     *
     * @return string
     */
    protected function unifyExtension($extension)
    {
        $extension = strtolower($extension);
        switch ($extension) {
            case 'jpeg':
                return 'jpg';

            default:
                return $extension;
        }
    }
}
