<?php

declare(strict_types=1);

namespace MeetMatt\OpenApiSpecCoverage\Specification;

class TypeArray extends TypeAbstract
{
    private TypeAbstract $type;

    public function __construct(TypeAbstract $type)
    {
        $this->type = $type;
    }

    public function getType(): TypeAbstract
    {
        return $this->type;
    }
}
