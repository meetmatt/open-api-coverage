<?php

declare(strict_types=1);

namespace MeetMatt\OpenApiSpecCoverage\Specification;

class Path extends CoverageElement
{
    private array $matchedPathParameters = [];

    /** @var Operation[] */
    private array $operations = [];

    public function __construct(private readonly string $uriPath)
    {
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
        if ($uriPath === $this->uriPath) {
            return true;
        }

        if (preg_match($this->getUriPathAsRegex(), $uriPath, $matches) === 1) {
            $this->matchedPathParameters = array_filter($matches, 'is_string', ARRAY_FILTER_USE_KEY);

            return true;
        }

        return false;
    }

    public function getMatchedPathParameters(): array
    {
        return $this->matchedPathParameters;
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

    public function operation(string $httpMethod): ?Operation
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
                        sprintf('/(?<%s>%s)', $pathParameter->getName(), $type->asRegex()),
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
