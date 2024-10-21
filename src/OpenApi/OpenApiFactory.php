<?php

declare(strict_types=1);

namespace MeetMatt\OpenApiSpecCoverage\OpenApi;

use MeetMatt\OpenApiSpecCoverage\Specification\Specification;
use MeetMatt\OpenApiSpecCoverage\Specification\SpecificationFactoryInterface;

class OpenApiFactory implements SpecificationFactoryInterface
{
    public function __construct(private readonly OpenApiReader $reader, private readonly OpenApiSpecificationParser $parser)
    {
    }

    public function fromFile(string $filePath): Specification
    {
        $openApi = $this->reader->readFromFile($filePath);

        return $this->parser->parsePaths($openApi);
    }
}
