<?php

declare(strict_types=1);

namespace MeetMatt\OpenApiSpecCoverage\Specification;

class Specification
{
    /** @var array<string, Path> */
    private array $paths;

    public function __construct()
    {
        $this->paths = [];
    }

    public function addPath(Path $path): void
    {
        $this->paths[$path->getUriPath()] = $path;
    }

    public function getPaths(): array
    {
        return $this->paths;
    }

    public function findPath(string $uriPath): ?Path
    {
        return $this->paths[$uriPath] ?? null;
    }
}
