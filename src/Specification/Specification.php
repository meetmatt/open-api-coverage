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
    public function path(string $uriPath): ?Path
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
            // TODO: match operation by ID passed via TestRecorder
            throw SpecificationException::uriMatchedMultiplePaths($matches);
        }

        return current($matches);
    }
}
