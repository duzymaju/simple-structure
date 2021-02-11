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
     * Get raw
     *
     * @param string $name         name
     * @param mixed  $defaultValue default value
     *
     * @return mixed
     */
    public function getRaw($name, $defaultValue = null)
    {
        return $this->has($name) ? $this->params[$name] : $defaultValue;
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
        if ($this->has($name)) {
            return $this->params[$name];
        }

        foreach ($this->parentPacks as $parentPack) {
            $value = $parentPack->get($name);
            if (isset($value)) {
                return $value;
            }
        }

        return $defaultValue;
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
        $value = $this->get($name);

        return isset($value) ? Parser::parseString($value, $length) : $defaultValue;
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
        $value = $this->get($name);

        return isset($value) ? Parser::parseInt($value) : $defaultValue;
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
        $value = $this->get($name);

        return isset($value) ? Parser::parseFloat($value) : $defaultValue;
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
        $value = $this->get($name);

        return isset($value) ? Parser::parseBool($value) : $defaultValue;
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
        $value = $this->get($name);

        return isset($value) ? Parser::parseObject($value) : $defaultValue;
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
        $value = $this->get($name);

        return isset($value) ? Parser::parseArray($value) : $defaultValue;
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
        $value = $this->get($name);

        return isset($value) ? Parser::parseStringArray($value, $length) : $defaultValue;
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
        $value = $this->get($name);

        return isset($value) ? Parser::parseIntArray($value) : $defaultValue;
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
        $value = $this->get($name);

        return isset($value) ? Parser::parseFloatArray($value) : $defaultValue;
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
        $value = $this->get($name);

        return isset($value) ? Parser::parseBoolArray($value) : $defaultValue;
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
        $value = $this->get($name);
        $numericParam = is_numeric($value) ? +$value : null;
        if (in_array($numericParam, $availableValues)) {
            $value = $numericParam;
        } elseif (!in_array($value, $availableValues)) {
            $value = $defaultValue;
        }

        return $value;
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
     * Get values
     *
     * @return array
     */
    public function getValues()
    {
        return array_values($this->params);
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
        return array_key_exists($name, $this->params);
    }
}
