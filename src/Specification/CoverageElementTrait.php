<?php

declare(strict_types=1);

namespace MeetMatt\OpenApiSpecCoverage\Specification;

trait CoverageElementTrait
{
    protected bool $isDocumented = true;

    protected bool $isExecuted   = false;

    protected bool $isAsserted   = false;

    public function isDocumented(): bool
    {
        return $this->isDocumented;
    }

    public function setIsDocumented(bool $isDocumented): self
    {
        $this->isDocumented = $isDocumented;

        return $this;
    }

    public function isExecuted(): bool
    {
        return $this->isExecuted;
    }

    public function setIsExecuted(bool $isExecuted): self
    {
        $this->isExecuted = $isExecuted;

        return $this;
    }

    public function isAsserted(): bool
    {
        return $this->isAsserted;
    }

    public function setIsAsserted(bool $isAsserted): self
    {
        $this->isAsserted = $isAsserted;

        return $this;
    }
}
