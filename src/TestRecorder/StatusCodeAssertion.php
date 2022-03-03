<?php

declare(strict_types=1);

namespace MeetMatt\OpenApiSpecCoverage\TestRecorder;

class StatusCodeAssertion
{
    private string $statusCode;

    public function __construct(string $statusCode)
    {
        $this->statusCode = $statusCode;
    }

    public function getStatusCode(): string
    {
        return $this->statusCode;
    }
}
