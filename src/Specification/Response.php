<?php

namespace MeetMatt\OpenApiSpecCoverage\Specification;

class Response
{
    /** @var string */
    private $httpStatusCode;

    /** @var array<string, ResponseBody> */
    private $responseBodies;

    /**
     * @param string $httpStatusCode
     */
    public function __construct($httpStatusCode)
    {
        $this->httpStatusCode = $httpStatusCode;
        $this->responseBodies = [];
    }

    /**
     * @return string
     */
    public function getHttpStatusCode()
    {
        return $this->httpStatusCode;
    }

    /**
     * @param ResponseBody $responseBody
     */
    public function addResponseBody(ResponseBody $responseBody)
    {
        $this->responseBodies[$responseBody->getContentType()] = $responseBody;
    }

    /**
     * @param string $contentType
     *
     * @return ResponseBody|null
     */
    public function findResponseBody($contentType)
    {
        return isset($this->responseBodies[$contentType]) ? $this->responseBodies[$contentType] : null;
    }

    /**
     * @return array<string, ResponseBody>
     */
    public function getResponseBodies()
    {
        return $this->responseBodies;
    }
}