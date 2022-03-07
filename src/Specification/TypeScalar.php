<?php

declare(strict_types=1);

namespace MeetMatt\OpenApiSpecCoverage\Specification;

class TypeScalar extends TypeAbstract
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
}
