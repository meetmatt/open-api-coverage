<?php

namespace MeetMatt\OpenApiSpecCoverage\Specification;

class Path
{
    /** @var string */
    private $url;

    /** @var array<string, Operation> */
    private $operations;

    /**
     * @param string $url
     */
    public function __construct($url)
    {
        $this->url        = $url;
        $this->operations = [];
    }

    /**
     * @return string
     */
    public function getUrl()
    {
        return $this->url;
    }

    /**
     * @param Operation $operation
     */
    public function addOperation(Operation $operation)
    {
        $this->operations[$operation->getHttpMethod()] = $operation;
    }

    /**
     * @param string $method
     *
     * @return Operation|null
     */
    public function findOperation($method)
    {
        return isset($this->operations[$method]) ? $this->operations[$method] : null;
    }

    /**
     * @return array<string, Operation>
     */
    public function getOperations()
    {
        return $this->operations;
    }
}
