<?php

declare(strict_types=1);

namespace MeetMatt\OpenApiSpecCoverage\Specification;

class Operation extends CoverageElement
{
    private string $httpMethod;

    /** @var array<Parameter> */
    private array $pathParameters;

    /** @var array<Parameter> */
    private array $queryParameters;

    /** @var array<RequestBody> */
    private array $requestBodies;

    /** @var array<Response> */
    private array $responses;

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
     * @return array<Parameter>
     */
    public function getPathParameters(): array
    {
        return $this->pathParameters;
    }

    /**
     * @return array<Parameter>
     */
    public function getQueryParameters(): array
    {
        return $this->queryParameters;
    }

    /**
     * @return array<RequestBody>
     */
    public function getRequestBodies(): array
    {
        return $this->requestBodies;
    }

    /**
     * @return array<Response>
     */
    public function getResponses(): array
    {
        return $this->responses;
    }

    public function addPathParameter(Parameter $parameter): self
    {
        $this->pathParameters[] = $parameter;

        return $this;
    }

    public function addQueryParameter(Parameter $parameter): self
    {
        $this->queryParameters[] = $parameter;

        return $this;
    }

    public function addRequestBody(RequestBody $requestBody): self
    {
        $this->requestBodies[] = $requestBody;

        return $this;
    }

    public function addResponse(Response $response): self
    {
        $this->responses[] = $response;

        return $this;
    }

    public function findQueryParameter(string $queryParameterName, TypeAbstract $queryParameterType): ?Parameter
    {
        foreach ($this->queryParameters as $queryParameter) {
            if (
                $queryParameter->getName() === $queryParameterName
                &&
                $queryParameter->getType() === $queryParameterType
            ) {
                return $queryParameter;
            }
        }

        return null;
    }
}
