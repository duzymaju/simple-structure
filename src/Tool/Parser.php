<?php

namespace SimpleStructure\Tool;

/**
 * Parser tool
 */
class Parser
{
    /**
     * Parse string
     *
     * @param mixed    $value  value
     * @param int|null $length length
     *
     * @return string
     */
    public static function parseString($value, $length = null)
    {
        return is_int($length) && $length > 0 ? mb_substr((string) $value, 0, $length) : (string) $value;
    }

    /**
     * Parse integer
     *
     * @param mixed $value value
     *
     * @return int
     */
    public static function parseInt($value)
    {
        return (int) $value;
    }

    /**
     * Parse float
     *
     * @param mixed $value value
     *
     * @return float
     */
    public static function parseFloat($value)
    {
        return (float) (is_string($value) && preg_match('#^-?[0-9]*,[0-9]+$#', $value) ?
            str_replace(',', '.', $value) : $value);
    }

    /**
     * Parse boolean
     *
     * @param mixed $value value
     *
     * @return bool
     */
    public static function parseBool($value)
    {
        return !in_array($value, ['false', 'null', '', '0']) || false;
    }

    /**
     * Parse object
     *
     * @param mixed $values values
     *
     * @return object
     */
    public static function parseObject($values)
    {
        return (object) $values;
    }

    /**
     * Parse array
     *
     * @param mixed $values values
     *
     * @return array
     */
    public static function parseArray($values)
    {
        return (array) $values;
    }

    /**
     * Parse string array
     *
     * @param mixed    $values values
     * @param int|null $length length
     *
     * @return array
     */
    public static function parseStringArray($values, $length = null)
    {
        return array_map(function ($value) use ($length) {
            return self::parseString($value, $length);
        }, self::parseArray($values));
    }

    /**
     * Parse integer array
     *
     * @param mixed $values values
     *
     * @return array
     */
    public static function parseIntArray($values)
    {
        return array_map(function ($value) {
            return self::parseInt($value);
        }, self::parseArray($values));
    }

    /**
     * Parse float array
     *
     * @param mixed $values values
     *
     * @return array
     */
    public static function parseFloatArray($values)
    {
        return array_map(function ($value) {
            return self::parseFloat($value);
        }, self::parseArray($values));
    }

    /**
     * Parse boolean array
     *
     * @param mixed $values values
     *
     * @return array
     */
    public static function parseBoolArray($values)
    {
        return array_map(function ($value) {
            return self::parseBool($value);
        }, self::parseArray($values));
    }
}
