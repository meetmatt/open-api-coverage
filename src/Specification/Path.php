<?php

declare(strict_types=1);

namespace MeetMatt\OpenApiSpecCoverage\Specification;

class Path extends CoverageElement
{
    private string $uriPath;

    /** @var Operation[] */
    private array $operations = [];

    public function __construct(string $uriPath)
    {
        $this->uriPath = $uriPath;
    }

    public function addOperation(string $httpMethod): Operation
    {
        $operation = new Operation($httpMethod);

        $this->operations[] = $operation;

        return $operation;
    }

    /**
     * @throws SpecificationException
     */
    public function matches(string $uriPath): bool
    {
        return preg_match($this->getUriPathAsRegex(), $uriPath) === 1;
    }

    public function getUriPath(): string
    {
        return $this->uriPath;
    }

    /**
     * @return Operation[]
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

    /**
     * @throws SpecificationException
     */
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
            throw SpecificationException::pathHasAmbiguousPathParameters($this->getUriPath());
        }

        return "#^$pathUris[0]$#";
    }
}
