<?php

declare(strict_types=1);

namespace MeetMatt\OpenApiSpecCoverage;

use MeetMatt\OpenApiSpecCoverage\OpenApi\OpenApiFactory;
use MeetMatt\OpenApiSpecCoverage\OpenApi\OpenApiReader;
use MeetMatt\OpenApiSpecCoverage\OpenApi\OpenApiSchemaParser;
use MeetMatt\OpenApiSpecCoverage\OpenApi\OpenApiSpecificationParser;
use MeetMatt\OpenApiSpecCoverage\Specification\Specification;

class Coverage
{
    private Specification  $spec;

    private InputCriteria  $input;

    private OutputCriteria $output;

    public function __construct(string $specFile)
    {
        $factory = new OpenApiFactory(
            new OpenApiReader(),
            new OpenApiSpecificationParser(
                new OpenApiSchemaParser()
            )
        );

        $this->spec = $factory->fromFile($specFile);
    }

    public function spec(): Specification
    {
        return $this->spec;
    }

    public function input(): InputCriteria
    {
        return $this->input;
    }

    public function output(): OutputCriteria
    {
        return $this->output;
    }
}