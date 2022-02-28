<?php

declare(strict_types=1);

namespace MeetMatt\OpenApiSpecCoverage\Specification;

class TypeObject extends TypeAbstract
{
    /** @var array<string, Property> */
    private array $properties;

    public function __construct(array $properties)
    {
        $this->properties = $properties;
    }

    /**
     * @return array<string, Property>
     */
    public function getProperties(): array
    {
        return $this->properties;
    }
}