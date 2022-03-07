<?php

declare(strict_types=1);

namespace MeetMatt\OpenApiSpecCoverage\Specification;

class RequestBody
{
    private string $contentType;

    /** @var array<Property> */
    private array $properties;

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
        $this->properties[] = $property;
    }

    /**
     * @return array<Property>
     */
    public function getProperties(): array
    {
        return $this->properties;
    }
}
