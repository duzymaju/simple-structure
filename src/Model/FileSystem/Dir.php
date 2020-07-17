<?php

namespace SimpleStructure\Model\FileSystem;

use SimpleStructure\Exception\InvalidArgumentException;
use SimpleStructure\Exception\RuntimeException;

/**
 * Dir model
 */
class Dir
{
    /** @var string */
    protected $path;

    /**
     * Construct
     *
     * @param string $path                path
     * @param bool   $createIfDoesntExist create if doesn't exist
     *
     * @throws InvalidArgumentException
     */
    public function __construct($path, $createIfDoesntExist = false)
    {
        if (empty($path)) {
            throw new InvalidArgumentException('Dir path is empty!');
        } else if (!is_dir($path)) {
            if ($createIfDoesntExist) {
                mkdir($path, 0777, true);
            } else {
                throw new InvalidArgumentException('Dir path is invalid!');
            }
        }

        $this->path = rtrim($path, '/');
    }

    /**
     * Get children names
     *
     * @return string[]
     */
    public function getChildrenNames()
    {
        return array_filter(scandir($this->path), function ($name) {
            return !in_array($name, ['.', '..']);
        });
    }

    /**
     * Get children
     *
     * @return (Dir|File)[]
     */
    public function getChildren()
    {
        return array_map(function ($path) {
            return is_dir($path) ? new Dir($path) : File::create($path);
        }, $this->getChildrenPaths());
    }

    /**
     * Get children paths
     *
     * @return string[]
     */
    public function getChildrenPaths()
    {
        return array_map(function ($name) {
            return $this->path . '/' . $name;
        }, $this->getChildrenNames());
    }

    /**
     * Get file paths
     *
     * @return string[]
     */
    public function getFilePaths()
    {
        return array_filter($this->getChildrenPaths(), function ($path) {
            return !is_dir($path);
        });
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
        foreach ($this->getChildrenPaths() as $childPath) {
            if (is_dir($childPath)) {
                $dir = new Dir($childPath);
                $dir->delete();
            } else {
                unlink($childPath);
            }
        }
        rmdir($this->path);

        return null;
    }
}
