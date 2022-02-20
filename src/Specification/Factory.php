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

        // empty spec?
        if (!is_iterable($openApi->paths)) {
            return $spec;
        }

        // each path is an API endpoint
        foreach ($openApi->paths as $pathId => $path) {
            $specPath = new Path($pathId);
            $spec->addPath($specPath);

            // each operation on the path is an HTTP method of the endpoint
            $operations = $path->getOperations();
            foreach ($operations as $httpMethod => $operation) {
                $specOperation = new Operation($httpMethod);
                $specPath->addOperation($specOperation);

                // request parameters
                if (isset($operation->parameters) && is_iterable($operation->parameters)) {
                    foreach ($operation->parameters as $parameter) {
                        $type = $parameter->schema->type;

                        $values = self::flattenParameterValues($parameter);

                        $specParameter = new Parameter($parameter->name, $type);
                        $specParameter->setValues($values);

                        // query parameter (can be scalar, array and object)
                        if ($parameter->in === 'query') {
                            $specOperation->addQueryParameter($specParameter);
                        }

                        // path parameter (can be only scalar)
                        if ($parameter->in === 'path') {
                            $specOperation->addPathParameter($specParameter);
                        }
                    }
                }

                // request body object
                if (isset($operation->requestBody) && is_iterable($operation->requestBody->content)) {
                    foreach ($operation->requestBody->content as $contentType => $requestBody) {
                        $specRequestBody = new RequestBody($contentType);
                        $specOperation->addRequestBody($specRequestBody);

                        if (!is_iterable($requestBody->schema)) {
                            continue;
                        }

                        foreach ($requestBody->schema as $propertyName => $propertyDefinition) {
                            $specPropertyValues = !empty($propertyDefinition->enum) ? $propertyDefinition->enum : [];
                            $specProperty       = new Property($propertyName, $specPropertyValues);
                            $specRequestBody->addProperty($specProperty);
                        }
                    }
                }

                // responses
                if (!is_iterable($operation->responses)) {
                    continue;
                }
                foreach ($operation->responses as $httpStatusCode => $response) {
                    $specResponse = new Response($httpStatusCode);
                    $specOperation->addResponse($specResponse);

                    if (!is_iterable($response->content)) {
                        continue;
                    }

                    foreach ($response->content as $contentType => $content) {
                        $specResponseBody = new ResponseBody($contentType);
                        $specResponse->addResponseBody($specResponseBody);

                        foreach ($content->schema->properties as $propertyName => $propertyDefinition) {
                            $specPropertyValues = !empty($propertyDefinition->enum) ? $propertyDefinition->enum : [];
                            $specProperty       = new Property($propertyName, $specPropertyValues);
                            $specResponseBody->addProperty($specProperty);
                        }
                    }
                }
            }
        }

        // TODO: tags, tag coverage?

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

    /**
     * Checks if array is an associative array object or a simple list.
     */
    private static function isObject(array $value): bool
    {
        $keys = array_keys($value);

        return is_string(array_shift($keys));
    }
}