<?php

declare(strict_types=1);

namespace MeetMatt\OpenApiSpecCoverage\Test\Support;

use MeetMatt\OpenApiSpecCoverage\OpenApi\OpenApiFactory;
use MeetMatt\OpenApiSpecCoverage\OpenApi\OpenApiReader;
use MeetMatt\OpenApiSpecCoverage\OpenApi\OpenApiSchemaParser;
use MeetMatt\OpenApiSpecCoverage\OpenApi\OpenApiSpecificationParser;

class Container
{
    public function specFile(): string
    {
        return codecept_data_dir('petstore.yaml');
    }

    public function factory(): OpenApiFactory
    {
        return new OpenApiFactory(
            new OpenApiReader(),
            new OpenApiSpecificationParser(
                new OpenApiSchemaParser()
            )
        );
    }
}
