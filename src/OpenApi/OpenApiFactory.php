<?php

declare(strict_types=1);

namespace MeetMatt\OpenApiSpecCoverage\OpenApi;

use MeetMatt\OpenApiSpecCoverage\Specification\Specification;
use MeetMatt\OpenApiSpecCoverage\Specification\SpecificationFactoryInterface;

class OpenApiFactory implements SpecificationFactoryInterface
{
    private OpenApiReader $reader;

    private OpenApiSpecificationParser $parser;

    public function __construct(OpenApiReader $reader, OpenApiSpecificationParser $parser)
    {
        $this->reader = $reader;
        $this->parser = $parser;
    }

    public function fromFile(string $filePath): Specification
    {
        $openApi = $this->reader->readFromFile($filePath);

        $specification = new Specification();

        foreach ($openApi->paths as $uriPath => $spec) {
            $path = $specification->addPath($uriPath);
            foreach ($spec->getOperations() as $httpMethod => $openApiOperation) {
                $operation = $path->addOperation($httpMethod);

                $this->parser->parseParameters($operation, $openApiOperation);
                $this->parser->parseRequests($operation, $openApiOperation);
                $this->parser->parseResponses($operation, $openApiOperation);
            }
        }

        return $specification;
    }
}
