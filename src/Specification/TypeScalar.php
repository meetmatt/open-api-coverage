<?php

declare(strict_types=1);

namespace MeetMatt\OpenApiSpecCoverage\Specification;

use RuntimeException;

class TypeScalar extends TypeAbstract implements RegexSerializable
{
    private string $type;

    /** @var float|int|string|null */
    private $value;

    /**
     * @param string                $type
     * @param float|int|string|null $value
     */
    public function __construct(string $type, $value = null)
    {
        $this->type  = $type;
        $this->value = $value;
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
        switch ($this->type) {
            case 'string':
                // forward slashes and dots don't work correctly in path parameters
                return '[^./]+';

            case 'number':
            case 'integer':
                return '\d+';

            case 'boolean':
            case 'array':
            case 'object':
            default:
                throw new RuntimeException(sprintf('Unsupported parameter type: %s', $this->type));
        }
    }
}
