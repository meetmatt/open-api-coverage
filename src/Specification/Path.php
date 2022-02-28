<?php

declare(strict_types=1);

namespace MeetMatt\OpenApiSpecCoverage\Specification;

class Path
{
    private string $httpPath;

    /** @var array<string, Operation> */
    private $operations;

    public function __construct(string $httpPath)
    {
        $this->httpPath   = $httpPath;
        $this->operations = [];
    }

    public function getHttpPath(): string
    {
        return $this->httpPath;
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
}
