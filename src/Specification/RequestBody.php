<?php

declare(strict_types=1);

namespace MeetMatt\OpenApiSpecCoverage\Specification;

class RequestBody extends CoverageElement implements Typed
{
    use TypedTrait;

    private string $contentType;

    public function __construct(string $contentType, TypeAbstract $type)
    {
        $this->contentType = $contentType;
        $this->type        = $type;
    }

    public function getContentType(): string
    {
        return $this->contentType;
    }
}
