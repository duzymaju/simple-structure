<?php

namespace SimpleStructure\Model\FileSystem;

use CURLFile;
use finfo;
use SimpleStructure\Exception\BadMethodCallException;
use SimpleStructure\Exception\InvalidArgumentException;
use SimpleStructure\Exception\RuntimeException;

/**
 * File model
 */
class File extends FileAbstract
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
     * @return File|CsvFile|ZipFile
     */
    public static function create($path, $baseName = null, $size = null, $error = null, $temporary = false)
    {
        $pathInfo = pathinfo(empty($baseName) ? $path : $baseName);
        $fileName = $pathInfo['filename'];
        $extension = $pathInfo['extension'];

        switch ($extension) {
            case 'csv':
                return new CsvFile($path, $fileName, $extension, $size, $error, $temporary);

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
        $destination = $this->getDestination($dirPath, $fileName ?: $this->fileName, $this->extension, true);
        if ($this->isUploaded()) {
            throw new BadMethodCallException(sprintf('Uploaded file %s can not be copied.', $this->path));
        } else {
            if (!copy($this->path, $destination->path)) {
                throw new RuntimeException(sprintf('An error occurred during file %s copying.', $this->path));
            }
        }

        return new self($destination->path, $destination->fileName, $destination->extension, $this->size);
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
        $destination = $this->getDestination($dirPath, $fileName ?: $this->fileName, $this->extension, true);
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
        return new CURLFile($this->path, $this->getContentType(), $this->getFileNameWithExt());
    }

    /**
     * Exists
     *
     * @return bool
     */
    public function exists()
    {
        return is_file($this->path);
    }

    /**
     * Is uploaded
     *
     * @return bool
     */
    public function isUploaded()
    {
        return $this->temporary && is_uploaded_file($this->path);
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
        return isset($this->size) ? $this->size : filesize($this->path);
    }

    /**
     * Get content
     *
     * @return string|false
     */
    public function getContent()
    {
        return file_get_contents($this->path);
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
        return isset($this->error) && $this->error != UPLOAD_ERR_OK;
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
}
