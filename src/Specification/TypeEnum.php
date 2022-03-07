<?php

declare(strict_types=1);

namespace MeetMatt\OpenApiSpecCoverage\Specification;

class TypeEnum extends TypeAbstract
{
    private TypeScalar $scalarType;

    /** @var string[]|int[]|float[] */
    private array $enum;

    /** @var string[]|int[]|float[] */
    private array $executedEnum;

    public function __construct(TypeScalar $scalarType, array $enum)
    {
        $this->scalarType = $scalarType;
        $this->enum       = $enum;
    }

    public function getScalarType(): TypeScalar
    {
        return $this->scalarType;
    }

    /**
     * @return float[]|int[]|string[]
     */
    public function getEnum(): array
    {
        return $this->enum;
    }

    /**
     * @param float|int|string $value
     */
    public function setEnumValueAsExecuted($value): void
    {
        $this->executedEnum[] = $value;
    }

    /**
     * @return float[]|int[]|string[]
     */
    public function getExecutedEnum(): array
    {
        return $this->executedEnum;
    }
}
