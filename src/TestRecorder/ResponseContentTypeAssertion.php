<?php

declare(strict_types=1);

namespace MeetMatt\OpenApiSpecCoverage\TestRecorder;

class ResponseContentTypeAssertion
{
    private string $contentType;

    public function __construct(string $contentType)
    {
        $this->contentType = $contentType;
    }

    public function getContentType(): string
    {
        return $this->contentType;
    }
}
