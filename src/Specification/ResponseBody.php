<?php

namespace MeetMatt\OpenApiSpecCoverage\Specification;

class ResponseBody
{
    /** @var string */
    private $contentType;

    /** @var array<string, Property */
    private $properties;

    /**
     * @param string $contentType
     */
    public function __construct($contentType)
    {
        $this->contentType = $contentType;
        $this->properties  = [];
    }

    /**
     * @return string
     */
    public function getContentType()
    {
        return $this->contentType;
    }

    /**
     * @return array<string, Property>
     */
    public function getProperties()
    {
        return $this->properties;
    }

    /**
     * @param Property $property
     */
    public function addProperty(Property $property)
    {
        $this->properties[$property->getPath()] = $property;
    }

    /**
     * @param string $path
     *
     * @return Property|null
     */
    public function findProperty($path)
    {
        return isset($this->properties[$path]) ? $this->properties[$path] : null;
    }
}