<?php

namespace MeetMatt\OpenApiSpecCoverage\Specification;

class Parameter
{
    private string $name;

    private string $type;

    private $values;

    public function __construct(string $name, string $type)
    {
        $this->name   = $name;
        $this->type   = $type;
        $this->values = null;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setValues($values): void
    {
        $this->values = $values;
    }
}