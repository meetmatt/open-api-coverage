<?php

declare(strict_types=1);

namespace MeetMatt\OpenApiSpecCoverage\Specification;

class Response extends CoverageElement
{
    private string $statusCode;

    /** @var Content[] */
    private array $contents = [];

    public function __construct(string $statusCode)
    {
        $this->statusCode = $statusCode;
    }

    public function getStatusCode(): string
    {
        return $this->statusCode;
    }

    public function addContent(Content $content): void
    {
        $this->contents[] = $content;
    }

    /**
     * @return Content[]
     */
    public function getContents(): array
    {
        return $this->contents;
    }
}
