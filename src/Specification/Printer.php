<?php

declare(strict_types=1);

namespace MeetMatt\OpenApiSpecCoverage\Specification;

class Printer
{
    public function print(Specification $specification): void
    {
        foreach ($specification->getPaths() as $path) {
            echo "\n{$path->getUriPath()}";
            foreach ($path->getOperations() as $operation) {
                $types = [];
                foreach ($operation->getPathParameters() as $parameter) {
                    $types += self::flattenTypeTree($parameter->getName(), $parameter->getType());
                }
                foreach ($operation->getQueryParameters() as $parameter) {
                    $types += self::flattenTypeTree($parameter->getName(), $parameter->getType());
                }
                foreach ($operation->getResponses() as $response) {
                    foreach ($response->getContents() as $responseBody) {
                        foreach ($responseBody->getProperties() as $property) {
                            $types += self::flattenTypeTree($property->getName(), $property->getType());
                        }
                    }
                }
                foreach ($operation->getRequestBodies() as $requestBody) {
                    foreach ($requestBody->getProperties() as $property) {
                        $types += self::flattenTypeTree($property->getName(), $property->getType());
                    }
                }
                echo "\n  {$operation->getHttpMethod()}";
                $longestNameLength = 0;
                foreach ($types as $key => $value) {
                    $len = strlen($key);
                    if ($longestNameLength < $len) {
                        $longestNameLength = $len;
                    }
                }
                foreach ($types as $key => $value) {
                    $padding = str_repeat(' ', $longestNameLength - strlen($key));
                    echo "\n    $key$padding = $value";
                }
            }
            echo "\n";
        }
        echo "\n";
    }

    private static function flattenTypeTree(string $name, TypeAbstract $type): array
    {
        $flat = [];

        if ($type instanceof TypeScalar) {
            $flat[$name] = '<' . $type->getType() . '>';
        } elseif ($type instanceof TypeEnum) {
            /** @noinspection JsonEncodingApiUsageInspection */
            $flat[$name] = '<' . $type->getScalarType()->getType() . '>' . json_encode($type->getEnum());
        } elseif ($type instanceof TypeArray) {
            foreach (self::flattenTypeTree($name . '[]', $type->getType()) as $key => $value) {
                $flat[$key] = $value;
            }
        } elseif ($type instanceof TypeObject) {
            foreach ($type->getProperties() as $property) {
                $propertyPrefix    = $name . '.' . $property->getName();
                $flattenedProperty = self::flattenTypeTree($propertyPrefix, $property->getType());
                foreach ($flattenedProperty as $key => $value) {
                    $flat[$key] = $value;
                }
            }
        }

        return $flat;
    }
}
