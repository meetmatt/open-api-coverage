<?php

declare(strict_types=1);

namespace MeetMatt\OpenApiSpecCoverage\Specification;

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
}
