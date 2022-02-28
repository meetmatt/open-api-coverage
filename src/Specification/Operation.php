<?php

declare(strict_types=1);

namespace MeetMatt\OpenApiSpecCoverage\Specification;

class Operation
{
    private string $httpMethod;

    /** @var array<string, Parameter> */
    private $pathParameters;

    /** @var array<string, Parameter> */
    private $queryParameters;

    /** @var array<string, RequestBody> */
    private $requestBodies;

    /** @var array<string, Response> */
    private $responses;

    public function __construct(string $httpMethod)
    {
        $this->httpMethod      = $httpMethod;
        $this->pathParameters  = [];
        $this->queryParameters = [];
        $this->requestBodies   = [];
        $this->responses       = [];
    }

    public function getHttpMethod(): string
    {
        return $this->httpMethod;
    }

    /**
     * @return array<string, Parameter>
     */
    public function getPathParameters(): array
    {
        return $this->pathParameters;
    }

    /**
     * @return array<string, Parameter>
     */
    public function getQueryParameters(): array
    {
        return $this->queryParameters;
    }

    /**
     * @return array<string, RequestBody>
     */
    public function getRequestBodies(): array
    {
        return $this->requestBodies;
    }

    /**
     * @return array<string, Response>
     */
    public function getResponses(): array
    {
        return $this->responses;
    }

    public function addPathParameter(Parameter $parameter): void
    {
        $this->pathParameters[$parameter->getName()] = $parameter;
    }

    public function addQueryParameter(Parameter $parameter): void
    {
        $this->queryParameters[$parameter->getName()] = $parameter;
    }

    public function addRequestBody(RequestBody $requestBody): void
    {
        $this->requestBodies[$requestBody->getContentType()] = $requestBody;
    }

    public function addResponse(Response $response): void
    {
        $this->responses[$response->getHttpStatusCode()] = $response;
    }
}
