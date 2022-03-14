<?php

declare(strict_types=1);

namespace MeetMatt\OpenApiSpecCoverage\Specification;

trait TypedTrait
{
    private TypeAbstract $type;

    public function getType(): TypeAbstract
    {
        return $this->type;
    }
}
