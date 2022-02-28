<?php

declare(strict_types=1);

namespace MeetMatt\OpenApiSpecCoverage\OpenApi;

use cebe\openapi\spec\OpenApi;
use cebe\openapi\spec\Operation as OpenApiOperation;
use MeetMatt\OpenApiSpecCoverage\Specification\Content;
use MeetMatt\OpenApiSpecCoverage\Specification\Operation;
use MeetMatt\OpenApiSpecCoverage\Specification\Parameter;
use MeetMatt\OpenApiSpecCoverage\Specification\Path;
use MeetMatt\OpenApiSpecCoverage\Specification\Property;
use MeetMatt\OpenApiSpecCoverage\Specification\RequestBody;
use MeetMatt\OpenApiSpecCoverage\Specification\Response;

class OpenApiSpecificationParser
{
    private OpenApiSchemaParser $parser;

    public function __construct(OpenApiSchemaParser $parser)
    {
        $this->parser = $parser;
    }

    /**
     * @param OpenApi $openApi
     *
     * @return Path[]
     */
    public function parsePaths(OpenApi $openApi): array
    {
        $paths = [];

        foreach ($openApi->paths as $httpPath => $openApiPath) {
            $path = new Path($httpPath);
            foreach ($openApiPath->getOperations() as $method => $operation) {
                $path->addOperation($this->parseOperation($method, $operation));
            }
            $paths[] = $path;
        }

        return $paths;
    }

    private function parseOperation(string $httpMethod, OpenApiOperation $openApiOperation): Operation
    {
        $operation = new Operation($httpMethod);

        $this->parseParameters($operation, $openApiOperation);
        $this->parseRequests($operation, $openApiOperation);
        $this->parseResponses($operation, $openApiOperation);

        return $operation;
    }

    private function parseParameters(Operation $operation, OpenApiOperation $openApiOperation): void
    {
        if (!isset($openApiOperation->parameters) || !is_iterable($openApiOperation->parameters)) {
            return;
        }

        foreach ($openApiOperation->parameters as $openApiParameter) {
            $parameter = new Parameter($openApiParameter->name, $this->parser->parse($openApiParameter->schema));

            if ($openApiParameter->in === 'query') {
                $operation->addQueryParameter($parameter);
            }

            if ($openApiParameter->in === 'path') {
                $operation->addPathParameter($parameter);
            }
        }
    }

    private function parseRequests(Operation $operation, OpenApiOperation $openApiOperation): void
    {
        if (!isset($openApiOperation->requestBody->content) || !is_iterable($openApiOperation->requestBody->content)) {
            return;
        }

        foreach ($openApiOperation->requestBody->content as $contentType => $mediaType) {
            $specRequestBody = new RequestBody($contentType);
            $operation->addRequestBody($specRequestBody);

            $name = 'requestBody.' . $contentType;
            $prop = new Property($name, $this->parser->parse($mediaType->schema));
            $specRequestBody->addProperty($prop);
        }
    }

    private function parseResponses(Operation $operation, OpenApiOperation $openApiOperation): void
    {
        if (!isset($openApiOperation->responses) || !is_iterable($openApiOperation->responses)) {
            return;
        }

        foreach ($openApiOperation->responses as $httpStatusCode => $openApiResponse) {
            $response = new Response((string)$httpStatusCode);
            $operation->addResponse($response);

            if (!isset($openApiResponse->content) || !is_iterable($openApiResponse->content)) {
                continue;
            }

            $this->parseResponseContents((string)$httpStatusCode, $response, $openApiResponse);
        }
    }

    private function parseResponseContents(string $httpStatusCode, Response $response, $openApiResponse): void
    {
        foreach ($openApiResponse->content as $contentType => $openApiResponseContent) {
            $content = new Content($contentType);
            $response->addContent($content);

            if (!isset($openApiResponseContent->schema->properties) || !is_iterable($openApiResponseContent->schema->properties)) {
                continue;
            }

            foreach ($openApiResponseContent->schema->properties as $propertyName => $propertySchema) {
                $name = 'response.' . $httpStatusCode . '.' . $contentType . '.' . $propertyName;
                $type = $this->parser->parse($propertySchema);
                $prop = new Property($name, $type);
                $content->addProperty($prop);
            }
        }
    }
}