<?php

declare(strict_types=1);

namespace MeetMatt\OpenApiSpecCoverage\Specification;

use RuntimeException;

class Specification
{
    /** @var array<Path> */
    private array $paths;

    public function __construct()
    {
        $this->paths = [];
    }

    public function addPath(Path $path): self
    {
        $this->paths[] = $path;

        return $this;
    }

    public function getPaths(): array
    {
        return $this->paths;
    }

    public function findPath(string $uriPath): ?Path
    {
        $matches = [];
        foreach ($this->paths as $path) {
            if ($path->matches($uriPath)) {
                $matches[] = $path;
            }
        }

        if (empty($matches)) {
            return null;
        }

        if (count($matches) > 1) {
            throw new RuntimeException('URI matched more than one path: ' . implode(', ', array_keys($matches)));
        }

        return array_pop($matches);
    }
}
