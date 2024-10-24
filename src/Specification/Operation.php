<?php

declare(strict_types=1);

namespace MeetMatt\OpenApiSpecCoverage\Specification;

class Operation extends CoverageElement
{
    /** @var Parameter[] */
    private array $pathParameters = [];

    /** @var Parameter[] */
    private array $queryParameters = [];

    /** @var RequestBody[] */
    private array $requestBodies = [];

    /** @var Response[] */
    private array $responses = [];

    public function __construct(private readonly string $httpMethod)
    {
    }

    public function getHttpMethod(): string
    {
        return $this->httpMethod;
    }

    /**
     * @return Parameter[]
     */
    public function getPathParameters(): array
    {
        return $this->pathParameters;
    }

    /**
     * @param string $name
     *
     * @return Parameter[]
     */
    public function findPathParameters(string $name): array
    {
        return array_filter(
            $this->pathParameters,
            static fn(Parameter $parameter) => $parameter->getName() === $name
        );
    }

    /**
     * @return Parameter[]
     */
    public function getQueryParameters(): array
    {
        return $this->queryParameters;
    }

    /**
     * @return RequestBody[]
     */
    public function getRequestBodies(): array
    {
        return $this->requestBodies;
    }

    /**
     * @return Response[]
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

    public function findQueryParameter(string $queryParameterName, ?TypeAbstract $queryParameterType = null): ?Parameter
    {
        foreach ($this->queryParameters as $queryParameter) {
            if (
                $queryParameter->getName() === $queryParameterName
                && (
                    $queryParameterType === null
                    || $queryParameter->getType()::class === $queryParameterType::class
                )
            ) {
                return $queryParameter;
            }
        }

        return null;
    }

    public function findRequestBody(string $contentType): ?RequestBody
    {
        foreach ($this->requestBodies as $requestBody) {
            if ($requestBody->getContentType() === $contentType) {
                return $requestBody;
            }
        }

        return null;
    }

    public function findResponseByStatusCode(string $statusCode): ?Response
    {
        foreach ($this->responses as $response) {
            if ($response->getStatusCode() === $statusCode) {
                return $response;
            }
        }

        return null;
    }
}
