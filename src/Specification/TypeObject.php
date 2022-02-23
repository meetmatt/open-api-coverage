<?php

namespace MeetMatt\OpenApiSpecCoverage\Specification;

class TypeObject extends TypeAbstract
{
    /** @var Property[] */
    private array $properties;

    public function __construct(array $properties)
    {
        $this->properties = $properties;
    }

    public function getProperties(): array
    {
        return $this->properties;
    }
}