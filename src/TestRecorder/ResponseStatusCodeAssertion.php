<?php

declare(strict_types=1);

namespace MeetMatt\OpenApiSpecCoverage\TestRecorder;

class ResponseStatusCodeAssertion
{
    public function __construct(private readonly string $statusCode)
    {
    }

    public function getStatusCode(): string
    {
        return $this->statusCode;
    }
}
