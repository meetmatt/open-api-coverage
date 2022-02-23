<?php

namespace MeetMatt\OpenApiSpecCoverage\Specification;

use cebe\openapi\exceptions\IOException;
use cebe\openapi\exceptions\TypeErrorException;
use cebe\openapi\exceptions\UnresolvableReferenceException;
use cebe\openapi\json\InvalidJsonPointerSyntaxException;
use cebe\openapi\Reader;
use cebe\openapi\ReferenceContext;
use cebe\openapi\spec\OpenApi;
use Exception;
use MeetMatt\OpenApiSpecCoverage\Util\Util;

class Factory
{
    /**
     * @param string $specFile
     *
     * @return Specification
     *
     * @throws Exception
     */
    public static function fromFile(string $specFile): Specification
    {
        $specId  = self::generateId($specFile);
        $openApi = self::loadSpecFromFile($specFile);

        $spec = new Specification($specId);

        if (!is_iterable($openApi->paths)) {
            return $spec;
        }

        foreach ($openApi->paths as $route => $path) {
            $specPath = new Path($route);
            $spec->addPath($specPath);

            foreach ($path->getOperations() as $method => $operation) {
                $specOperation = new Operation($method);
                $specPath->addOperation($specOperation);

                if (isset($operation->parameters) && is_iterable($operation->parameters)) {
                    $parameters = $operation->parameters;
                    foreach ($parameters as $parameter) {
                        $name = $parameter->name;

                        // parameters with [] in the name will be treated as arrays
                        if (strpos($name, '[]') === strlen($name) - 2) {
                            $name = substr($name, 0, -2);
                        }

                        $typeTree = Util::buildTypeTree($parameter->schema);
                        $param    = new Parameter($name, $typeTree);

                        if ($parameter->in === 'query') {
                            $specOperation->addQueryParameter($param);
                        }
                        if ($parameter->in === 'path') {
                            $specOperation->addPathParameter($param);
                        }
                    }
                }

                if (isset($operation->requestBody->content) && is_iterable($operation->requestBody->content)) {
                    foreach ($operation->requestBody->content as $key => $mediaType) {
                        $specRequestBody = new RequestBody($key);
                        $specOperation->addRequestBody($specRequestBody);

                        $name     = 'requestBody.' . $key;
                        $typeTree = Util::buildTypeTree($mediaType->schema);
                        $prop     = new Property($name, $typeTree);
                        $specRequestBody->addProperty($prop);
                    }
                }

                if (isset($operation->responses) && is_iterable($operation->responses)) {
                    foreach ($operation->responses as $code => $response) {
                        $specResponse = new Response($code);
                        $specOperation->addResponse($specResponse);

                        if (isset($response->content) && is_iterable($response->content)) {
                            foreach ($response->content as $contentType => $content) {
                                $specResponseBody = new ResponseBody($contentType);
                                $specResponse->addResponseBody($specResponseBody);

                                foreach ($content->schema->properties as $propertyName => $propertyDefinition) {
                                    $name     = 'response.' . $code . '.' . $contentType . '.' . $propertyName;
                                    $typeTree = Util::buildTypeTree($propertyDefinition);
                                    $prop     = new Property($name, $typeTree);
                                    $specResponseBody->addProperty($prop);
                                }
                            }
                        }
                    }
                }
            }
        }

        // TODO: tags?

        return $spec;
    }

    /**
     * @param string $specFile
     *
     * @return string
     */
    private static function generateId($specFile)
    {
        return basename($specFile);
    }

    /**
     * @param string $specFile
     *
     * @return OpenApi
     *
     * @throws IOException
     * @throws TypeErrorException
     * @throws UnresolvableReferenceException
     * @throws InvalidJsonPointerSyntaxException
     */
    private static function loadSpecFromFile(string $specFile): OpenApi
    {
        if (!file_exists($specFile)) {
            throw new Exception("File doesn't exist: $specFile");
        }

        $type = strtolower(pathinfo($specFile, PATHINFO_EXTENSION));

        switch ($type) {
            case 'yml':
            case 'yaml':
                return Reader::readFromYamlFile($specFile, OpenApi::class, ReferenceContext::RESOLVE_MODE_ALL);

            case 'json':
                return Reader::readFromJsonFile($specFile, OpenApi::class, ReferenceContext::RESOLVE_MODE_ALL);

            default:
                throw new Exception("Unsupported spec format: $type. Supported formats: yml/yaml, json.");
        }
    }

    private static function flattenParameterValues(\cebe\openapi\spec\Parameter $parameter): ?array
    {
        $values = [];


        return $values;
    }

    public static function flatten(
        $schema,
        string $delimiter = '.',
        array &$list = [],
        string $prefix = '$.',
        bool $isArray = false
    ): array {
        // array of enum - $parameter->schema->items->enum
        // object - $parameter->schema->properties
        // scalar
        // if (isset($parameter->schema->enum) && is_iterable($parameter->schema->enum)) {
        //   $values = $parameter->schema->enum;
        // } else {
        //   $values = null;
        //}

        foreach ($schema as $key => $value) {
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

    /**
     * Checks if array is an associative array object or a simple list.
     */
    private static function isObject(array $value): bool
    {
        $keys = array_keys($value);

        return is_string(array_shift($keys));
    }
}