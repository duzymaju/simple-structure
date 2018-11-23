<?php

namespace SimpleStructure\Tool;

/**
 * Param pack tool
 */
class ParamPack
{
    /** @var array */
    protected $params;

    /** @var array */
    protected $parentPacks = [];

    /**
     * Constructor
     *
     * @param array $params params
     */
    public function __construct(array $params = [])
    {
        $this->params = $params;
    }

    /**
     * Add parent pack
     *
     * @param self $parentPack parent pack
     *
     * @return self
     */
    public function addParentPack(ParamPack $parentPack)
    {
        $this->parentPacks[] = $parentPack;

        return $this;
    }

    /**
     * Get
     *
     * @param string $name         name
     * @param mixed  $defaultValue default value
     *
     * @return mixed
     */
    public function get($name, $defaultValue = null)
    {
        if (array_key_exists($name, $this->params)) {
            $param = $this->stripSlashes($this->params[$name]);
        } else {
            foreach ($this->parentPacks as $parentPack) {
                $param = $parentPack->get($name);
                if (isset($param)) {
                    break;
                }
            }
            if (!isset($param)) {
                $param = $defaultValue;
            }
        }

        return $param;
    }

    /**
     * Get string
     *
     * @param string      $name         name
     * @param string|null $defaultValue default value
     * @param int|null    $length       length
     *
     * @return string|null
     */
    public function getString($name, $defaultValue = null, $length = null)
    {
        $param = $this->get($name);
        $result = isset($param) ? Parser::parseString($param, $length) : $defaultValue;

        return $result;
    }

    /**
     * Get integer
     *
     * @param string   $name         name
     * @param int|null $defaultValue default value
     *
     * @return int|null
     */
    public function getInt($name, $defaultValue = null)
    {
        $param = $this->get($name);
        $result = isset($param) ? Parser::parseInt($param) : $defaultValue;

        return $result;
    }

    /**
     * Get float
     *
     * @param string     $name         name
     * @param float|null $defaultValue default value
     *
     * @return float|null
     */
    public function getFloat($name, $defaultValue = null)
    {
        $param = $this->get($name);
        $result = isset($param) ? Parser::parseFloat($param) : $defaultValue;

        return $result;
    }

    /**
     * Get boolean
     *
     * @param string    $name         name
     * @param bool|null $defaultValue default value
     *
     * @return bool|null
     */
    public function getBool($name, $defaultValue = null)
    {
        $param = $this->get($name);
        $result = isset($param) ? Parser::parseBool($param) : $defaultValue;

        return $result;
    }

    /**
     * Get object
     *
     * @param string      $name         name
     * @param object|null $defaultValue default value
     *
     * @return object|null
     */
    public function getObject($name, $defaultValue = null)
    {
        $param = $this->get($name);
        $result = isset($param) ? Parser::parseObject($param) : $defaultValue;

        return $result;
    }

    /**
     * Get array
     *
     * @param string $name         name
     * @param array  $defaultValue default value
     *
     * @return array
     */
    public function getArray($name, $defaultValue = [])
    {
        $param = $this->get($name);
        $result = isset($param) ? Parser::parseArray($param) : $defaultValue;

        return $result;
    }

    /**
     * Get string array
     *
     * @param string   $name         name
     * @param string[] $defaultValue default value
     * @param int|null $length       length
     *
     * @return string[]
     */
    public function getStringArray($name, $defaultValue = [], $length = null)
    {
        $param = $this->get($name);
        $result = isset($param) ? Parser::parseStringArray($param, $length) : $defaultValue;

        return $result;
    }

    /**
     * Get integer array
     *
     * @param string $name         name
     * @param int[]  $defaultValue default value
     *
     * @return int[]
     */
    public function getIntArray($name, $defaultValue = [])
    {
        $param = $this->get($name);
        $result = isset($param) ? Parser::parseIntArray($param) : $defaultValue;

        return $result;
    }

    /**
     * Get float array
     *
     * @param string  $name         name
     * @param float[] $defaultValue default value
     *
     * @return float[]
     */
    public function getFloatArray($name, $defaultValue = [])
    {
        $param = $this->get($name);
        $result = isset($param) ? Parser::parseFloatArray($param) : $defaultValue;

        return $result;
    }

    /**
     * Get boolean array
     *
     * @param string $name         name
     * @param bool[] $defaultValue default value
     *
     * @return bool[]
     */
    public function getBoolArray($name, $defaultValue = [])
    {
        $param = $this->get($name);
        $result = isset($param) ? Parser::parseBoolArray($param) : $defaultValue;

        return $result;
    }

    /**
     * Get option
     *
     * @param string $name            name
     * @param array  $availableValues available values
     * @param mixed  $defaultValue    default value
     *
     * @return mixed
     */
    public function getOption($name, array $availableValues, $defaultValue = null)
    {
        $param = $this->get($name);
        $numericParam = is_numeric($param) ? +$param : null;
        if (in_array($numericParam, $availableValues)) {
            $param = $numericParam;
        } elseif (!in_array($param, $availableValues)) {
            $param = $defaultValue;
        }

        return $param;
    }

    /**
     * Add
     *
     * @param string $name  name
     * @param mixed  $value value
     *
     * @return self
     */
    public function add($name, $value)
    {
        $this->params[$name] = $value;

        return $this;
    }

    /**
     * Get pack
     *
     * @return array
     */
    public function getPack()
    {
        return $this->params;
    }

    /**
     * Has
     * 
     * @param string $name name
     *
     * @return bool
     */
    public function has($name)
    {
        $param = $this->get($name);

        return isset($param);
    }

    /**
     * Strip slashes
     *
     * @param mixed $value value
     *
     * @return mixed
     */
    private function stripSlashes($value)
    {
        if (function_exists('get_magic_quotes_gpc') && get_magic_quotes_gpc()) {
            if (is_array($value)) {
                $value = $this->stripSlashesRecursively($value);
            } elseif (is_string($value)) {
                $value = stripslashes($value);
            }
        }

        return $value;
    }

    /**
     * Strip slashes recursively
     *
     * @param array $array array
     *
     * @return array
     */
    private function stripSlashesRecursively(array $array)
    {
        foreach ($array as $key => $value) {
            if (is_array($value)) {
                $array[$key] = $this->stripSlashesRecursively($value);
            } elseif (is_string($value)) {
                $array[$key] = stripslashes($value);
            }
        }

        return $array;
    }
}
