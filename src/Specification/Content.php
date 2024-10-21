<?php

declare(strict_types=1);

namespace MeetMatt\OpenApiSpecCoverage\Specification;

class Content extends CoverageElement
{
    use PropertyTrait;

    public function __construct(private string $contentType)
    {
    }

    public function getContentType(): string
    {
        return $this->contentType;
    }
}
