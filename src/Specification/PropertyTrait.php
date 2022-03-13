<?php

declare(strict_types=1);

namespace MeetMatt\OpenApiSpecCoverage\Specification;

trait PropertyTrait
{
    /** @var Property[] */
    private array $properties = [];

    /**
     * @param Property|string $nameOrProperty
     * @param TypeAbstract|null $type
     *
     * @return Property
     */
    public function addProperty($nameOrProperty, TypeAbstract $type = null): Property
    {
        if (is_string($nameOrProperty)) {
            $nameOrProperty = new Property($nameOrProperty, $type);
        }

        $this->properties[] = $nameOrProperty;

        return $nameOrProperty;
    }

    /**
     * @return Property[]
     */
    public function getProperties(): array
    {
        return $this->properties;
    }
}
