<?php

declare(strict_types=1);

namespace MeetMatt\OpenApiSpecCoverage\Specification;

class Printer
{
    public function print(Specification $specification): void
    {
        foreach ($specification->getPaths() as $route => $path) {
            echo "\n$route";
            foreach ($path->getOperations() as $method => $operation) {
                $types = [];
                foreach ($operation->getPathParameters() as $name => $parameter) {
                    $types += self::flattenTypeTree($name, $parameter->getType());
                }
                foreach ($operation->getQueryParameters() as $name => $parameter) {
                    $types += self::flattenTypeTree($name, $parameter->getType());
                }
                foreach ($operation->getResponses() as $statusCode => $response) {
                    foreach ($response->getContents() as $contentType => $responseBody) {
                        foreach ($responseBody->getProperties() as $name => $property) {
                            $types += self::flattenTypeTree($name, $property->getType());
                        }
                    }
                }
                foreach ($operation->getRequestBodies() as $contentType => $requestBody) {
                    foreach ($requestBody->getProperties() as $name => $property) {
                        $types += self::flattenTypeTree($name, $property->getType());
                    }
                }
                echo "\n  $method";
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
}