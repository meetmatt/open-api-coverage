<?php

namespace MeetMatt\OpenApiSpecCoverage\Specification;

class Operation
{
    /** @var string */
    private $httpMethod;

    /** @var array<string, Parameter> */
    private $pathParameters;

    /** @var array<string, Parameter> */
    private $queryParameters;

    /** @var array<string, RequestBody> */
    private $requestBodies;

    /** @var array<string, Response> */
    private $responses;

    /**
     * @param string $httpMethod
     */
    public function __construct($httpMethod)
    {
        $this->httpMethod     = $httpMethod;
        $this->pathParameters = [];
        $this->queryParameters = [];
        $this->requestBodies   = [];
        $this->responses       = [];
    }

    /**
     * @return string
     */
    public function getHttpMethod()
    {
        return $this->httpMethod;
    }

    /**
     * @return array<string, Parameter>
     */
    public function getPathParameters()
    {
        return $this->pathParameters;
    }

    /**
     * @return array<string, Parameter>
     */
    public function getQueryParameters()
    {
        return $this->queryParameters;
    }

    /**
     * @return array<string, RequestBody>
     */
    public function getRequestBodies()
    {
        return $this->requestBodies;
    }

    /**
     * @return array<string, Response>
     */
    public function getResponses()
    {
        return $this->responses;
    }

    /**
     * @param Parameter $parameter
     */
    public function addPathParameter(Parameter $parameter)
    {
        $this->pathParameters[$parameter->getName()] = $parameter;
    }

    /**
     * @param Parameter $parameter
     */
    public function addQueryParameter(Parameter $parameter)
    {
        $this->queryParameters[$parameter->getName()] = $parameter;
    }

    /**
     * @param RequestBody $requestBody
     */
    public function addRequestBody(RequestBody $requestBody)
    {
        $this->requestBodies[$requestBody->getContentType()] = $requestBody;
    }

    /**
     * @param Response $response
     */
    public function addResponse(Response $response)
    {
        $this->responses[$response->getHttpStatusCode()] = $response;
    }

    /**
     * @param string $name
     *
     * @return Parameter|null
     */
    public function findPathParameter($name)
    {
        return isset($this->pathParameters[$name]) ? $this->pathParameters[$name] : null;
    }

    /**
     * @param string $name
     *
     * @return Parameter|null
     */
    public function findQueryParameter($name)
    {
        return isset($this->queryParameters[$name]) ? $this->queryParameters[$name] : null;
    }

    /**
     * @param string $contentType
     *
     * @return RequestBody|null
     */
    public function findRequestBody($contentType)
    {
        return isset($this->requestBodies[$contentType]) ? $this->requestBodies[$contentType] : null;
    }

    /**
     * @param string $httpStatusCode
     *
     * @return Response|null
     */
    public function findResponse($httpStatusCode)
    {
        return isset($this->responses[$httpStatusCode]) ? $this->responses[$httpStatusCode] : null;
    }
}
