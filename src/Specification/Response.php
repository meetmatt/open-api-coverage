<?php

declare(strict_types=1);

namespace MeetMatt\OpenApiSpecCoverage\Specification;

class Response
{
    private string $httpStatusCode;

    /** @var array<Content> */
    private array $contents;

    public function __construct(string $httpStatusCode)
    {
        $this->httpStatusCode = $httpStatusCode;
        $this->contents       = [];
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
     * @return array<Content>
     */
    public function getContents(): array
    {
        return $this->contents;
    }
}
