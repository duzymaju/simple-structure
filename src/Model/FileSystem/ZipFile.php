<?php

namespace SimpleStructure\Model\FileSystem;

use SimpleStructure\Exception\RuntimeException;
use ZipArchive;

/**
 * ZIP file model
 */
class ZipFile extends File
{
    /**
     * Unzip to
     *
     * @param string $dirPath dir path
     *
     * @return Dir
     */
    public function unzipTo($dirPath = '.')
    {
        if (!is_dir($dirPath)) {
            mkdir($dirPath, 0777, true);
        }
        $zip = new ZipArchive();
        if ($zip->open($this->path) !== true) {
            throw new RuntimeException('Unable to open ZIP archive.');
        }
        $zip->extractTo($dirPath);
        $zip->close();

        return new Dir($dirPath);
    }
}
