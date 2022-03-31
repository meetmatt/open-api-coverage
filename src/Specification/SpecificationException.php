<?php

declare(strict_types=1);

namespace MeetMatt\OpenApiSpecCoverage\Specification;

use Exception;

class SpecificationException extends Exception
{
    public static function unsupportedSpecificationDefinition(string $reason): self
    {
        return new self(sprintf('Unsupported Open API specification: %s', $reason));
    }

    public static function pathHasAmbiguousPathParameters(string $uriPath): self
    {
        return new self(sprintf('Path has ambiguous path parameters: %s', $uriPath));
    }

    /**
     * @param Path[] $matches
     *
     * @return self
     */
    public static function uriMatchedMultiplePaths(array $matches): self
    {
        $paths = implode(', ', array_map(static fn(Path $path) => $path->getUriPath(), $matches));

        return new self(sprintf('URI matched more than one path: %s', $paths));
    }
}
