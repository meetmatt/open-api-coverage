<?php

declare(strict_types=1);

namespace MeetMatt\OpenApiSpecCoverage\Specification;

class TypeObject extends TypeAbstract
{
    /** @var array<Property> */
    private array $properties;

    /**
     * @param array<Property> $properties
     */
    public function __construct(array $properties = [])
    {
        $this->properties = [];

        foreach ($properties as $property) {
            $this->addProperty($property);
        }
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
