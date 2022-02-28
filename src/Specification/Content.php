<?php

declare(strict_types=1);

namespace MeetMatt\OpenApiSpecCoverage\Specification;

class Content
{
    private string $contentType;

    /** @var array<string, Property */
    private $properties;

    public function __construct(string $contentType)
    {
        $this->contentType = $contentType;
        $this->properties  = [];
    }

    public function getContentType(): string
    {
        return $this->contentType;
    }

    public function addProperty(Property $property): void
    {
        $this->properties[$property->getName()] = $property;
    }

    /**
     * @return array<string, Property>
     */
    public function getProperties(): array
    {
        return $this->properties;
    }
}