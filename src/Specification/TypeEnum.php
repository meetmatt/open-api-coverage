<?php

declare(strict_types=1);

namespace MeetMatt\OpenApiSpecCoverage\Specification;

class TypeEnum extends TypeAbstract
{
    private TypeScalar $type;

    /** @var string[]|int[]|float[] */
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

    /**
     * @return float[]|int[]|string[]
     */
    public function getEnum(): array
    {
        return $this->enum;
    }
}