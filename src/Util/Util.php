<?php

namespace MeetMatt\OpenApiSpecCoverage\Util;

use cebe\openapi\spec\Schema;
use MeetMatt\OpenApiSpecCoverage\Specification\Parameter;
use MeetMatt\OpenApiSpecCoverage\Specification\Property;
use MeetMatt\OpenApiSpecCoverage\Specification\TypeAbstract;
use MeetMatt\OpenApiSpecCoverage\Specification\TypeArray;
use MeetMatt\OpenApiSpecCoverage\Specification\TypeEnum;
use MeetMatt\OpenApiSpecCoverage\Specification\TypeObject;
use MeetMatt\OpenApiSpecCoverage\Specification\TypeScalar;

class Util
{
    /**
     * @param $openApi
     * @return array<string, array<string, array<Parameter|Property>>>
     */
    public static function buildSpecification($openApi): array
    {
        # Build params for each path
        $paths = [];
        foreach ($openApi->paths as $route => $path) {
            // TODO: create Path object
            $paths[$route] = [];
            foreach ($path->getOperations() as $method => $operation) {
                // TODO: build Request object
                $params = [];
                if (isset($operation->parameters) && is_iterable($operation->parameters)) {
                    $parameters = $operation->parameters;
                    foreach ($parameters as $parameter) {
                        $name = $parameter->name;
                        if (strpos($name, '[]') === strlen($name) - 2) {
                            $name = substr($name, 0, -2);
                        }
                        $typeTree      = self::buildTypeTree($parameter->schema);
                        $params[$name] = new Parameter($name, $typeTree);
                        // TODO: encapsulate the parameters in the Request object
                    }
                }

                if (isset($operation->requestBody, $operation->requestBody->content) && is_iterable(
                        $operation->requestBody->content
                    )) {
                    foreach ($operation->requestBody->content as $key => $mediaType) {
                        // TODO: build RequestBody object
                        $name          = 'requestBody.' . $key;
                        $typeTree      = self::buildTypeTree($mediaType->schema);
                        $params[$name] = new Property($name, $typeTree);
                    }
                }

                if (isset($operation->responses) && is_iterable($operation->responses)) {
                    foreach ($operation->responses as $code => $response) {
                        // TODO: build Response object
                        if (isset($response->content) && is_iterable($response->content)) {
                            foreach ($response->content as $contentType => $content) {
                                // TODO: build ResponseContent object
                                $name          = 'response.' . $code . '.' . $contentType;
                                $typeTree      = self::buildTypeTree($content->schema);
                                $params[$name] = new Property($name, $typeTree);
                            }
                        }
                    }
                }

                $paths[$route][$method] = $params;
            }
        }

        return $paths;
    }

    public static function buildTypeTree(Schema $schema): TypeAbstract
    {
        // TODO: oneOf
        // TODO: anyOf

        if (isset($schema->allOf) && is_iterable($schema->allOf)) {
            $properties = [];
            foreach ($schema->allOf as $scheme) {
                $object = self::buildTypeTree($scheme);
                if ($object instanceof TypeObject) {
                    foreach ($object->getProperties() as $name => $property) {
                        $properties[$name] = $property;
                    }
                }
                // elseif ...
                // TODO: allOf can be only objects, right?
            }

            return new TypeObject($properties);
        }

        switch ($schema->type) {
            case 'array':
                $type = new TypeArray(self::buildTypeTree($schema->items));
                break;

            case 'object':
                $properties = [];
                foreach ($schema->properties as $name => $property) {
                    $properties[$name] = new Property($name, self::buildTypeTree($property));
                }
                $type = new TypeObject($properties);
                break;

            default:
                $type = new TypeScalar($schema->type);
                if (isset($schema->enum) && is_iterable($schema->enum)) {
                    $enum = $schema->enum;
                    $type = new TypeEnum($type, $enum);
                }
        }

        return $type;
    }

    private static function flattenTypeTree(string $name, TypeAbstract $type): array
    {
        $flat = [];

        if ($type instanceof TypeScalar) {
            $flat[$name] = '<' . $type->getType() . '>';
        } elseif ($type instanceof TypeEnum) {
            $flat[$name] = '<' . $type->getScalarType()->getType() . '>' . json_encode($type->getEnum());
        } elseif ($type instanceof TypeArray) {
            foreach (self::flattenTypeTree($name . '[]', $type->getType()) as $key => $value) {
                $flat[$key] = $value;
            }
        } elseif ($type instanceof TypeObject) {
            foreach ($type->getProperties() as $propertyType) {
                $propertyPrefix    = $name . '.' . $propertyType->getName();
                $flattenedProperty = self::flattenTypeTree($propertyPrefix, $propertyType->getType());
                foreach ($flattenedProperty as $key => $value) {
                    $flat[$key] = $value;
                }
            }
        }

        return $flat;
    }

    /**
     * @param array<string, array<string, array<Parameter|Property>>> $paths
     */
    public static function printSpecification(array $paths): void
    {
        foreach ($paths as $route => $methods) {
            echo "\n{$route}";
            foreach ($methods as $method => $parameters) {
                $types = [];
                foreach ($parameters as $name => $parameter) {
                    $types += self::flattenTypeTree($name, $parameter->getType());
                }
                echo "\n  {$method}";
                $longestNameLength = 0;
                foreach ($types as $key => $value) {
                    $len = strlen($key);
                    if ($longestNameLength < $len) {
                        $longestNameLength = $len;
                    }
                }
                foreach ($types as $key => $value) {
                    $padding = str_repeat(' ', $longestNameLength - strlen($key));
                    echo "\n    ${key}${padding} = {$value}";
                }
            }
            echo "\n";
        }
        echo "\n";
    }

    /**
     * Converts a nested array to a list of paths.
     *
     * @param array $array
     * @param string $delimiter
     * @param array $list
     * @param string $prefix
     * @param bool $isArray
     *
     * @return array
     */
    private static function flatten(
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
                    $list += self::flatten(
                        $value,
                        $delimiter,
                        $list,
                        $prefix . $key . ($isArray ? ']' : '') . $delimiter
                    );
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