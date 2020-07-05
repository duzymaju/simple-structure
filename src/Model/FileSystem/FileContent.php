<?php

namespace SimpleStructure\Model\FileSystem;

use SimpleStructure\Exception\InvalidArgumentException;
use SimpleStructure\Exception\RuntimeException;

/**
 * File content model
 */
class FileContent extends FileAbstract
{
    /** @var string */
    protected $content;

    /**
     * Construct
     *
     * @param string $url     URL
     * @param string $content content
     *
     * @throws InvalidArgumentException
     */
    public function __construct($url, $content)
    {
        if (empty($url)) {
            throw new InvalidArgumentException('File URL is empty!');
        }
        if (empty($content)) {
            throw new InvalidArgumentException('File content is empty!');
        }

        $urlParts = explode('/', $url);
        $fileParts = explode('.', array_pop($urlParts));
        if (count($fileParts) < 2) {
            throw new InvalidArgumentException('File URL is invalid!');
        }

        $this->extension = array_pop($fileParts);
        $this->fileName = implode('.', $fileParts);
        $this->content = $content;
    }

    /**
     * Copy to
     *
     * @param string $dirPath   dir path
     * @param string $fileName  file name
     * @param string $extension extension
     *
     * @return File|ZipFile
     */
    public function copyTo($dirPath, $fileName, $extension)
    {
        $destination = $this->getDestination($dirPath, $fileName, $extension, true);
        if (!file_put_contents($destination->path, $this->content)) {
            throw new RuntimeException(
                sprintf('An error occurred during file %s copying.', $destination->path)
            );
        }

        return File::create($destination->path);
    }

    /**
     * Get content
     *
     * @return string
     */
    public function getContent()
    {
        return $this->content;
    }
}
