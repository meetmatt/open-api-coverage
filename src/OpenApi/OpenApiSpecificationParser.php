<?php

declare(strict_types=1);

namespace MeetMatt\OpenApiSpecCoverage\OpenApi;

use cebe\openapi\spec\Operation as OpenApiOperation;
use MeetMatt\OpenApiSpecCoverage\Specification\Content;
use MeetMatt\OpenApiSpecCoverage\Specification\Operation;
use MeetMatt\OpenApiSpecCoverage\Specification\Parameter;
use MeetMatt\OpenApiSpecCoverage\Specification\RequestBody;
use MeetMatt\OpenApiSpecCoverage\Specification\Response;
use MeetMatt\OpenApiSpecCoverage\Specification\TypeArray;

class OpenApiSpecificationParser
{
    private OpenApiSchemaParser $parser;

    public function __construct(OpenApiSchemaParser $parser)
    {
        $this->parser = $parser;
    }

    public function parseParameters(Operation $operation, OpenApiOperation $openApiOperation): void
    {
        if (!isset($openApiOperation->parameters) || !is_iterable($openApiOperation->parameters)) {
            return;
        }

        foreach ($openApiOperation->parameters as $openApiParameter) {
            $name = $openApiParameter->name;
            $type = $this->parser->parse($openApiParameter->schema);

            // force array type for parameters that end with []
            while (preg_match('/\[]$/', $name)) {
                $name = preg_replace('/\[]$/', '', $name);
                $type = new TypeArray($type);
            }

            $parameter = new Parameter($name, $type);

            if ($openApiParameter->in === 'query') {
                $operation->addQueryParameter($parameter);
            }

            if ($openApiParameter->in === 'path') {
                $operation->addPathParameter($parameter);
            }
        }
    }

    public function parseRequests(Operation $operation, OpenApiOperation $openApiOperation): void
    {
        if (!isset($openApiOperation->requestBody->content) || !is_iterable($openApiOperation->requestBody->content)) {
            return;
        }

        foreach ($openApiOperation->requestBody->content as $contentType => $mediaType) {
            $specRequestBody = new RequestBody($contentType, $this->parser->parse($mediaType->schema));
            $operation->addRequestBody($specRequestBody);
        }
    }

    public function parseResponses(Operation $operation, OpenApiOperation $openApiOperation): void
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

            if (
                !isset($openApiResponseContent->schema->properties)
                || !is_iterable($openApiResponseContent->schema->properties)
            ) {
                continue;
            }

            foreach ($openApiResponseContent->schema->properties as $propertyName => $propertySchema) {
                $name = 'response.' . $httpStatusCode . '.' . $contentType . '.' . $propertyName;
                $type = $this->parser->parse($propertySchema);
                $content->addProperty($name, $type);
            }
        }
    }
}
