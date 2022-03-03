<?php

declare(strict_types=1);

namespace MeetMatt\OpenApiSpecCoverage\Specification;

class Path
{
    private string $uriPath;

    /** @var array<string, Operation> */
    private $operations;

    public function __construct(string $uriPath)
    {
        $this->uriPath    = $uriPath;
        $this->operations = [];
    }

    public function getUriPath(): string
    {
        return $this->uriPath;
    }

    public function addOperation(Operation $operation): void
    {
        $this->operations[$operation->getHttpMethod()] = $operation;
    }

    /**
     * @return array<string, Operation>
     */
    public function getOperations(): array
    {
        return $this->operations;
    }

    public function findOperation(string $httpMethod): ?Operation
    {
        return $this->operations[$httpMethod] ?? null;
    }
}
