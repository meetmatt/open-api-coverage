<?php

declare(strict_types=1);

namespace MeetMatt\OpenApiSpecCoverage\Specification;

trait CoverageElementTrait
{
    protected bool $isDocumented = false;

    protected bool $isExecuted   = false;

    protected bool $isAsserted   = false;

    public function documented(): self
    {
        $this->isDocumented = true;

        return $this;
    }

    public function executed(): self
    {
        $this->isExecuted = true;

        return $this;
    }

    public function asserted(): void
    {
        $this->isAsserted = true;
    }

    public function isDocumented(): bool
    {
        return $this->isDocumented;
    }

    public function isExecuted(): bool
    {
        return $this->isExecuted;
    }

    public function isAsserted(): bool
    {
        return $this->isAsserted;
    }
}
