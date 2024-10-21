<?php

declare(strict_types=1);

namespace MeetMatt\OpenApiSpecCoverage\Specification;

class Parameter extends CoverageElement implements Typed
{
    use TypedTrait;

    public function __construct(private string $name, TypeAbstract $type)
    {
        $this->type = $type;
    }

    public function getName(): string
    {
        return $this->name;
    }
}
