<?php

declare(strict_types=1);

namespace MeetMatt\OpenApiSpecCoverage\Specification;

class Property extends CoverageElement implements Typed
{
    use TypedTrait;

    private string $name;

    public function __construct(string $name, TypeAbstract $type)
    {
        $this->name = $name;
        $this->type = $type;
    }

    public function getName(): string
    {
        return $this->name;
    }
}
