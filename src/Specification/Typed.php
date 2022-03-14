<?php

declare(strict_types=1);

namespace MeetMatt\OpenApiSpecCoverage\Specification;

interface Typed
{
    public function getType(): TypeAbstract;
}
