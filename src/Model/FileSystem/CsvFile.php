<?php

namespace SimpleStructure\Model\FileSystem;

/**
 * CSV file model
 */
class CsvFile extends File
{
    /**
     * BOM marker - it has to be doublequoted!
     *
     * @var string
     */
    const BOM = "\xef\xbb\xbf";

    /** @var resource|false */
    private $fileHandler = false;

    /** @var bool */
    private $useColumnNames = false;

    /** @var array */
    private $columnNames = [];

    /** @var int */
    private $length = 0;

    /** @var string */
    private $delimiter = ',';

    /** @var string */
    private $enclosure = '"';

    /** @var string */
    private $escape = '\\';

    /**
     * Use column names
     *
     * @param bool $useColumnNames use column names
     *
     * @return self
     */
    public function useColumnNames($useColumnNames = true)
    {
        $this->useColumnNames = $useColumnNames;

        return $this;
    }

    /**
     * Set length
     *
     * @param int $length length
     *
     * @return self
     */
    public function setLength($length)
    {
        $this->length = $length;

        return $this;
    }

    /**
     * Set delimiter
     *
     * @param string $delimiter delimiter
     *
     * @return self
     */
    public function setDelimiter($delimiter)
    {
        $this->delimiter = $delimiter;

        return $this;
    }

    /**
     * Set enclosure
     *
     * @param string $enclosure enclosure
     *
     * @return self
     */
    public function setEnclosure($enclosure)
    {
        $this->enclosure = $enclosure;

        return $this;
    }

    /**
     * Set escape
     *
     * @param string $escape escape
     *
     * @return self
     */
    public function setEscape($escape)
    {
        $this->escape = $escape;

        return $this;
    }

    /**
     * Get line
     *
     * @return array|null
     */
    public function getLine()
    {
        if ($this->fileHandler === false) {
            $this->fileHandler = fopen($this->getPath(), 'r');
            if ($this->fileHandler !== false) {
                $hasBom = fgets($this->fileHandler, 4) === self::BOM;
                if (!$hasBom) {
                    rewind($this->fileHandler);
                }
                if ($this->useColumnNames) {
                    $this->columnNames = fgetcsv(
                        $this->fileHandler, $this->length, $this->delimiter, $this->enclosure, $this->escape
                    ) ?: [];
                }
            }
        }

        if ($this->fileHandler !== false) {
            $data = fgetcsv($this->fileHandler, $this->length, $this->delimiter, $this->enclosure, $this->escape);
            if ($data !== false) {
                return $this->useColumnNames && $this->columnNames ? array_combine($this->columnNames, $data) : $data;
            }
            fclose($this->fileHandler);
            $this->fileHandler = false;
        }

        return null;
    }
}
