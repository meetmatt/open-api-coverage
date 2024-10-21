<?php

declare(strict_types=1);

namespace MeetMatt\OpenApiSpecCoverage\Specification;

use RuntimeException;

class TypeScalar extends TypeAbstract implements RegexSerializable
{
    /**
     * @param string                $type
     * @param float|int|string|null $value
     */
    public function __construct(private readonly string $type, private $value = null)
    {
    }

    public function getType(): string
    {
        return $this->type;
    }

    /**
     * @return float|int|string|null
     */
    public function getValue()
    {
        return $this->value;
    }

    public function asRegex(): string
    {
        return match ($this->type) {
            'string' => '[^./]+',
            'float', 'number' => '(0|[1-9]+)?\.\d*',
            'integer' => '\d+',
            default => throw new RuntimeException(sprintf('Unsupported parameter type: %s', $this->type)),
        };
    }
}
