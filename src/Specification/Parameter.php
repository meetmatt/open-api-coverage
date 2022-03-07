<?php

declare(strict_types=1);

namespace MeetMatt\OpenApiSpecCoverage\Specification;

class Parameter extends CoverageElement
{
    private string       $name;

    private TypeAbstract $type;

    public function __construct(string $name, TypeAbstract $type)
    {
        $this->name = $name;
        $this->type = $type;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getType(): TypeAbstract
    {
        return $this->type;
    }
}
