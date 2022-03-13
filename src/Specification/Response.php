<?php

declare(strict_types=1);

namespace MeetMatt\OpenApiSpecCoverage\Specification;

class Response
{
    private string $httpStatusCode;

    /** @var Content[] */
    private array $contents = [];

    public function __construct(string $httpStatusCode)
    {
        $this->httpStatusCode = $httpStatusCode;
    }

    public function getHttpStatusCode(): string
    {
        return $this->httpStatusCode;
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
