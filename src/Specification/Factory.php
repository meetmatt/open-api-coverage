<?php

namespace MeetMatt\OpenApiSpecCoverage\Specification;

use cebe\openapi\exceptions\IOException;
use cebe\openapi\exceptions\TypeErrorException;
use cebe\openapi\exceptions\UnresolvableReferenceException;
use cebe\openapi\json\InvalidJsonPointerSyntaxException;
use cebe\openapi\Reader;
use cebe\openapi\spec\OpenApi;
use Exception;

class Factory
{
    /**
     * @param string $specFile
     *
     * @throws Exception
     *
     * @return Specification
     */
    public static function fromFile($specFile)
    {
        $specId  = self::generateId($specFile);
        $openApi = self::loadSpecFromFile($specFile);

        $spec = new Specification($specId);
        foreach ($openApi->paths as $pathId => $path) {
            $specPath = new Path($pathId);
            $spec->addPath($specPath);

            foreach ($path->getOperations() as $operationId => $operation) {
                $specOperation = new Operation($operationId);
                $specPath->addOperation($specOperation);

                foreach ($operation->parameters as $parameter) {
                    $specParameterValues = !empty($parameter->schema->enum) ? $parameter->schema->enum : [];
                    $specParameter       = new Parameter($parameter->name, $specParameterValues);

                    if ($parameter->in === 'query') {
                        $specOperation->addQueryParameter($specParameter);
                    }
                    if ($parameter->in === 'path') {
                        $specOperation->addPathParameter($specParameter);
                    }
                }

                foreach ($operation->requestBody->content as $contentType => $requestBody) {
                    $specRequestBody = new RequestBody($contentType);
                    $specOperation->addRequestBody($specRequestBody);

                    foreach ($requestBody->schema as $propertyId => $property) {
                        $specPropertyValues = !empty($property->enum) ? $property->enum : [];
                        $specProperty       = new Property($propertyId, $specPropertyValues);
                        $specRequestBody->addProperty($specProperty);
                    }
                }

                foreach ($operation->responses as $responseId => $response) {
                    $specResponse = new Response($responseId);
                    $specOperation->addResponse($specResponse);

                    foreach ($response->content as $contentId => $content) {
                        $specResponseBody = new ResponseBody($contentId);
                        $specResponse->addResponseBody($specResponseBody);

                        foreach ($content->schema->properties as $propertyId => $property) {
                            $specPropertyValues = !empty($property->enum) ? $property->enum : [];
                            $specProperty       = new Property($propertyId, $specPropertyValues);
                            $specResponseBody->addProperty($specProperty);
                        }
                    }
                }
            }
        }

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
    private static function loadSpecFromFile($specFile)
    {
        if (!is_string($specFile) || !file_exists($specFile)) {
            throw new Exception("File doesn't exist: $specFile");
        }

        $type = strtolower(pathinfo($specFile, PATHINFO_EXTENSION));

        switch ($type) {
            case 'yml':
            case 'yaml':
                return Reader::readFromYamlFile($specFile);

            case 'json':
                return Reader::readFromJsonFile($specFile);

            default:
                throw new Exception("Unsupported spec format: $type. Supported formats: yml/yaml, json.");
        }
    }
}