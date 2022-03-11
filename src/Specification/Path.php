<?php

declare(strict_types=1);

namespace MeetMatt\OpenApiSpecCoverage\Specification;

use RuntimeException;

class Path extends CoverageElement
{
    private string $uriPath;

    /** @var array<Operation> */
    private array $operations;

    public function __construct(string $uriPath)
    {
        $this->uriPath    = $uriPath;
        $this->operations = [];
    }

    public function getUriPath(): string
    {
        return $this->uriPath;
    }

    public function matches(string $uriPath): bool
    {
        return preg_match($this->getUriPathAsRegex(), $uriPath) === 1;
    }

    public function addOperation(Operation $operation): self
    {
        $this->operations[] = $operation;

        return $this;
    }

    /**
     * @return array<Operation>
     */
    public function getOperations(): array
    {
        return $this->operations;
    }

    public function findOperation(string $httpMethod): ?Operation
    {
        foreach ($this->operations as $operation) {
            if ($operation->getHttpMethod() === $httpMethod) {
                return $operation;
            }
        }

        return null;
    }

    private function getUriPathAsRegex(): string
    {
        $pathUris = [];
        foreach ($this->getOperations() as $operation) {
            $uriPath        = $this->getUriPath();
            $pathParameters = $operation->getPathParameters();
            foreach ($pathParameters as $pathParameter) {
                $type = $pathParameter->getType();
                if ($type instanceof RegexSerializable) {
                    $uriPath = str_replace(
                        sprintf('/{%s}', $pathParameter->getName()),
                        sprintf('/%s', $type->asRegex()),
                        $uriPath
                    );
                }
            }

            $pathUris[] = $uriPath;
        }

        $pathUris = array_unique($pathUris);
        if (count($pathUris) > 1) {
            throw new RuntimeException('Path has operations with different path parameters: ' . $this->getUriPath());
        }

        return "#^$pathUris[0]$#";
    }
}
