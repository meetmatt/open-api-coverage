<?php

declare(strict_types=1);

namespace MeetMatt\OpenApiSpecCoverage\Specification;

class Specification
{
    /** @var array<Path> */
    private array $paths;

    public function __construct()
    {
        $this->paths = [];
    }

    public function addPath(Path $path): void
    {
        $this->paths[] = $path;
    }

    public function getPaths(): array
    {
        return $this->paths;
    }

    public function findPath(string $uriPath): ?Path
    {
        foreach ($this->paths as $path) {
            // TODO: figure out how to match dynamic path parameters
            // e.g. /v1/users/31337 should match /v1/users/{id}, but only if there aren't any duplicates
            if ($path->getUriPath() === $uriPath) {
                return $path;
            }
        }

        return null;
    }
}
