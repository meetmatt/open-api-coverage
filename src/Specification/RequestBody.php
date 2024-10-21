<?php

declare(strict_types=1);

namespace MeetMatt\OpenApiSpecCoverage\Specification;

class RequestBody extends CoverageElement implements Typed
{
    use TypedTrait;

    public function __construct(private string $contentType, TypeAbstract $type)
    {
        $this->type        = $type;
    }

    public function getContentType(): string
    {
        return $this->contentType;
    }
}
