<?php

namespace SimpleStructure\Model\FileSystem;

use CURLFile;
use finfo;
use SimpleStructure\Exception\BadMethodCallException;
use SimpleStructure\Exception\InvalidArgumentException;
use SimpleStructure\Exception\RuntimeException;
use stdClass;

/**
 * File model
 */
class File
{
    /** @var string */
    protected $path;

    /** @var string */
    protected $fileName;

    /** @var string */
    protected $extension;

    /** @var int|null */
    protected $size;

    /** @var int|null */
    protected $error;

    /** @var bool */
    protected $temporary;

    /**
     * Create
     *
     * @param string      $path      path
     * @param string|null $baseName  base name
     * @param int|null    $size      size
     * @param int|null    $error     error
     * @param bool        $temporary temporary
     *
     * @return self
     */
    public static function create($path, $baseName = null, $size = null, $error = null, $temporary = false)
    {
        if (empty($baseName)) {
            $pathInfo = pathinfo($path);
        } else {
            $pathInfo = pathinfo($baseName);
        }
        $fileName = $pathInfo['filename'];
        $extension = $pathInfo['extension'];

        switch ($extension) {
            case 'zip':
                return new ZipFile($path, $fileName, $extension, $size, $error, $temporary);

            default:
                return new File($path, $fileName, $extension, $size, $error, $temporary);
        }
    }

    /**
     * Construct
     *
     * @param string   $path      path
     * @param string   $fileName  file name
     * @param string   $extension extension
     * @param int|null $size      size
     * @param int|null $error     error
     * @param bool     $temporary temporary
     *
     * @throws InvalidArgumentException
     */
    public function __construct($path, $fileName, $extension, $size = null, $error = null, $temporary = false)
    {
        if (empty($path) || !is_file($path)) {
            throw new InvalidArgumentException('File path is invalid!');
        }

        $this->path = $path;
        $this->fileName = $fileName;
        $this->extension = $extension;
        $this->size = $size;
        $this->error = $error;
        $this->temporary = $temporary;
    }

    /**
     * Copy to
     *
     * @param string      $dirPath  dir path
     * @param string|null $fileName file name
     *
     * @return self
     */
    public function copyTo($dirPath, $fileName = null)
    {
        $destination = $this->getDestination($dirPath, $fileName, true);
        if ($this->isUploaded()) {
            throw new BadMethodCallException(sprintf('Uploaded file %s can not be copied.', $this->path));
        } else {
            if (!copy($this->path, $destination->path)) {
                throw new RuntimeException(sprintf('An error occurred during file %s copying.', $this->path));
            }
        }
        $copiedFile = new self($destination->path, $destination->fileName, $destination->extension, $this->size);

        return $copiedFile;
    }

    /**
     * Move to
     *
     * @param string      $dirPath  dir path
     * @param string|null $fileName file name
     *
     * @return self
     */
    public function moveTo($dirPath, $fileName = null)
    {
        $destination = $this->getDestination($dirPath, $fileName, true);
        if ($this->isUploaded()) {
            if (!move_uploaded_file($this->path, $destination->path)) {
                throw new RuntimeException(sprintf('An error occurred during uploaded file %s moving.',
                    $this->path));
            }
            $this->temporary = false;
        } else {
            if (!rename($this->path, $destination->path)) {
                throw new RuntimeException(sprintf('An error occurred during file %s moving.', $this->path));
            }
        }
        $this->path = $destination->path;
        $this->fileName = $destination->fileName;
        $this->extension = $destination->extension;
        $this->error = null;

        return $this;
    }

    /**
     * Delete
     *
     * @return null
     *
     * @throws RuntimeException
     */
    public function delete()
    {
        if (!unlink($this->path)) {
            throw new RuntimeException(sprintf('An error occurred during file %s deleting.', $this->path));
        }

        return null;
    }

    /**
     * Get CURL file
     *
     * @return CURLFile
     */
    public function getCurlFile()
    {
        $file = new CURLFile($this->path, $this->getContentType(), $this->getFileNameWithExt());

        return $file;
    }

    /**
     * Exists
     *
     * @return bool
     */
    public function exists()
    {
        $exists = is_file($this->path);

        return $exists;
    }

    /**
     * Get destination
     *
     * @param string      $dirPath      dir path
     * @param string|null $fileName     file name
     * @param bool        $generateDirs generate directories
     *
     * @return stdClass
     */
    protected function getDestination($dirPath, $fileName = null, $generateDirs = false)
    {
        $destination = new stdClass();
        $destination->fileName = isset($fileName) ? $fileName : $this->fileName;
        $destination->extension = $this->unifyExtension($this->extension);
        $dirPath = rtrim(str_replace('\\', '/', $dirPath), '/');
        $destination->path = $dirPath . '/' . $destination->fileName . '.' . $destination->extension;
        if ($generateDirs && !is_dir($dirPath)) {
            mkdir($dirPath, 0777, true);
        }

        return $destination;
    }

    /**
     * Is uploaded
     *
     * @return bool
     */
    public function isUploaded()
    {
        $isUploaded = $this->temporary && is_uploaded_file($this->path);

        return $isUploaded;
    }

    /**
     * Get path
     *
     * @return string
     */
    public function getPath()
    {
        return $this->path;
    }

    /**
     * Get file name
     *
     * @return string
     */
    public function getFileName()
    {
        return $this->fileName;
    }

    /**
     * Get file name with extension
     *
     * @return string
     */
    public function getFileNameWithExt()
    {
        return $this->fileName . '.' . $this->extension;
    }

    /**
     * Get extension
     *
     * @return string
     */
    public function getExtension()
    {
        return $this->extension;
    }

    /**
     * Extension matches
     *
     * @param string $extension extension
     *
     * @return bool
     */
    public function extensionMatches($extension)
    {
        return $this->unifyExtension($extension) == $this->unifyExtension($this->extension);
    }

    /**
     * Get size
     *
     * @return int|false
     */
    public function getSize()
    {
        $size = isset($this->size) ? $this->size : filesize($this->path);

        return $size;
    }

    /**
     * Get content
     *
     * @return string|false
     */
    public function getContent()
    {
        $content = file_get_contents($this->path);

        return $content;
    }

    /**
     * Get error
     *
     * @return int|null
     */
    public function getError()
    {
        return $this->error;
    }

    /**
     * Has error
     *
     * @return bool
     */
    public function hasError()
    {
        $hasError = isset($this->error) && $this->error != UPLOAD_ERR_OK;

        return $hasError;
    }

    /**
     * Get content type
     *
     * @return string|null
     */
    public function getContentType()
    {
        if (function_exists('mime_content_type')) {
            $contentType = mime_content_type($this->path);
        } elseif (class_exists('finfo') && method_exists(new finfo, 'file') &&
            $fileInfo = new finfo(FILEINFO_MIME)) {
            $contentType = $fileInfo->file($this->path);
        } else {
            switch ($this->unifyExtension($this->extension)) {
                case 'php':
                case 'txt':
                    $contentType = 'text/plain';
                    break;

                case 'htm':
                case 'html':
                    $contentType = 'text/html';
                    break;

                case 'css':
                    $contentType = 'text/css';
                    break;

                case 'js':
                    $contentType = 'text/javascript';
                    break;

                case 'png':
                    $contentType = 'image/png';
                    break;

                case 'jpg':
                case 'jpeg':
                    $contentType = 'image/jpeg';
                    break;

                case 'gif':
                    $contentType = 'image/gif';
                    break;

                case 'bmp':
                    $contentType = 'image/bmp';
                    break;

                case 'pdf':
                    $contentType = 'application/pdf';
                    break;

                case 'zip':
                    $contentType = 'application/zip';
                    break;

                case 'doc':
                case 'docx':
                    $contentType = 'application/msword';
                    break;

                case 'xls':
                case 'xlsx':
                    $contentType = 'application/vnd.ms-excel';
                    break;

                case 'ppt':
                case 'pptx':
                    $contentType = 'application/vnd.ms-powerpoint';
                    break;

                case 'exe':
                    $contentType = 'application/octet-stream';
                    break;

                default:
                    $contentType = 'application/x-unknown';
            }
        }
        if (is_bool($contentType)) {
            $contentType = null;
        }

        return $contentType;
    }

    /**
     * Unify extension
     *
     * @param string $extension extension
     *
     * @return string
     */
    private function unifyExtension($extension)
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
