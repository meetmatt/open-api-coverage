<?php

namespace MeetMatt\OpenApiSpecCoverage\Util;

class Util
{
    public static function diff(array $a, array $b): array
    {
        $differ = fn($first, $second) => self::typenize(
            self::compare(
                self::flatten($first),
                self::flatten($second)
            )
        );

        $aVsB = $differ($a, $b);
        $bVsA = $differ($b, $a);

        return self::compare($aVsB, $bVsA);
    }

    /**
     * @param array $array1
     * @param array $array2
     *
     * @return array
     */
    private static function compare(array $array1, array $array2): array
    {
        $result = [];

        foreach ($array1 as $key => $value) {
            if (!array_key_exists($key, $array2)) {
                $result[$key] = $value;
                continue;
            }

            if (is_array($value) && count($value) > 0) {
                $recursiveArrayDiff = self::compare($value, $array2[$key]);

                if (count($recursiveArrayDiff) > 0) {
                    $result[$key] = $recursiveArrayDiff;
                }

                continue;
            }

            $value1 = $value;
            $value2 = $array2[$key];

            if (is_float($value1) || is_float($value2)) {
                $value1 = (string)$value1;
                $value2 = (string)$value2;
            }

            if ($value1 != $value2) {
                $result[$key] = $value;
            }
        }

        return $result;
    }

    /**
     * Converts a nested array to a list of paths.
     *
     * @param array $array
     * @param string $delimiter
     * @param array $list
     * @param string $prefix
     *
     * @return array
     */
    public static function flatten(
        array $array,
        string $delimiter = '.',
        array &$list = [],
        string $prefix = '$.',
        bool $isArray = false
    ): array {
        foreach ($array as $key => $value) {
            if (is_array($value)) {
                if (self::isObject($value)) {
                    // assoc array
                    $list += self::flatten($value, $delimiter, $list, $prefix . $key . ($isArray ? ']' : '') . $delimiter);
                } else {
                    // list
                    $list += self::flatten($value, $delimiter, $list, $prefix . $key . '[', true);
                }
            } else {
                $list[$prefix . $key . ($isArray ? ']' : '')] = $value;
            }
        }

        return $list;
    }

    private static function typenize(array $array): array
    {
        foreach ($array as $key => $value) {
            $array[$key] = is_array($value) ? self::typenize($value) : gettype($value);
        }

        return $array;
    }

    /**
     * Checks if array is an associative array object or a simple list.
     */
    private static function isObject(array $value): bool
    {
        $keys = array_keys($value);
        foreach ($keys as $key) {
            if (is_string($key)) {
                return true;
            }
        }

        return false;
    }
}