<?php

declare(strict_types=1);

namespace MeetMatt\OpenApiSpecCoverage\Specification;

class TypeEnum extends TypeAbstract implements RegexSerializable, Typed
{
    use TypedTrait;

    /** @var string[]|int[]|float[] */
    private array $enum;

    /** @var string[]|int[]|float[] */
    private array $executedEnum = [];

    public function __construct(TypeScalar $type, array $enum)
    {
        $this->type = $type;
        $this->enum = $enum;
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
    public function getDocumentedExecutedEnum(): array
    {
        return array_values(array_intersect($this->enum, $this->executedEnum));
    }

    /**
     * @return float[]|int[]|string[]
     */
    public function getNotExecutedEnum(): array
    {
        return array_values(array_diff($this->enum, $this->executedEnum));
    }

    /**
     * @return float[]|int[]|string[]
     */
    public function getUndocumentedEnum(): array
    {
        return array_values(array_diff($this->executedEnum, $this->enum));
    }

    public function asRegex(): string
    {
        return sprintf('(%s)', implode('|', $this->enum));
    }
}
