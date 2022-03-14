<?php

declare(strict_types=1);

namespace MeetMatt\OpenApiSpecCoverage\Specification;

class TypeArray extends TypeAbstract implements Typed
{
    use TypedTrait;

    public function __construct(TypeAbstract $type)
    {
        $this->type = $type;
    }
}
