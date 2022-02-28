<?php

namespace MeetMatt\OpenApiSpecCoverage\Test\Suite\Integration\Specification;

use Codeception\Test\Unit;
use MeetMatt\OpenApiSpecCoverage\OpenApi\OpenApiFactory;
use MeetMatt\OpenApiSpecCoverage\OpenApi\OpenApiReader;
use MeetMatt\OpenApiSpecCoverage\OpenApi\OpenApiSchemaParser;
use MeetMatt\OpenApiSpecCoverage\OpenApi\OpenApiSpecificationParser;

class FactoryTest extends Unit
{
    public function testFromFile(): void
    {
        $factory = new OpenApiFactory(
            new OpenApiReader(),
            new OpenApiSpecificationParser(
                new OpenApiSchemaParser()
            )
        );

        $specification = $factory->fromFile(codecept_data_dir('petstore.yaml'));

        $this->assertCount(2, $specification->getPaths());
    }
}