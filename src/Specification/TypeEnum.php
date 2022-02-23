<?php

namespace MeetMatt\OpenApiSpecCoverage\Specification;

class TypeEnum extends TypeAbstract
{
    private TypeScalar $type;

    private array $enum;

    public function __construct(TypeScalar $type, array $enum)
    {
        $this->type = $type;
        $this->enum = $enum;
    }

    public function getScalarType(): TypeScalar
    {
        return $this->type;
    }

    public function getEnum(): array
    {
        return $this->enum;
    }
}