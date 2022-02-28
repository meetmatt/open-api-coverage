<?php

declare(strict_types=1);

namespace MeetMatt\OpenApiSpecCoverage\Specification;

interface SpecificationFactoryInterface
{
    public function fromFile(string $filePath): Specification;
}