<?php

declare(strict_types=1);

namespace MeetMatt\OpenApiSpecCoverage\Specification;

class Specification
{
    /** @var Path[] */
    private array $paths = [];

    /**
     * @return Path[]
     */
    public function getPaths(): array
    {
        return $this->paths;
    }

    public function addPath(string $uriPath): Path
    {
        $path = new Path($uriPath);

        $this->paths[] = $path;

        return $path;
    }

    /**
     * @throws SpecificationException
     */
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
            // TODO: match by operation ID
            throw SpecificationException::uriMatchedMultiplePaths($matches);
        }

        return current($matches);
    }
}
