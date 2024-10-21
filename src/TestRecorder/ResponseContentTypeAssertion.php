<?php

declare(strict_types=1);

namespace MeetMatt\OpenApiSpecCoverage\TestRecorder;

class ResponseContentTypeAssertion
{
    public function __construct(private readonly string $contentType)
    {
    }

    public function getContentType(): string
    {
        return $this->contentType;
    }
}
