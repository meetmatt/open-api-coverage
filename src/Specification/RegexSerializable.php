<?php

declare(strict_types=1);

namespace MeetMatt\OpenApiSpecCoverage\Specification;

interface RegexSerializable
{
    public function asRegex(): string;
}
