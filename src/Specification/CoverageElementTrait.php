<?php

declare(strict_types=1);

namespace MeetMatt\OpenApiSpecCoverage\Specification;

trait CoverageElementTrait
{
    protected bool $isDocumented = true;

    protected bool $isExecuted   = false;

    protected bool $isAsserted   = false;

    public function undocumented(): void
    {
        $this->isDocumented = false;
    }

    public function executed(): void
    {
        $this->isExecuted = true;
    }

    public function markAsAsserted(): void
    {
        $this->isAsserted = true;
    }
}
