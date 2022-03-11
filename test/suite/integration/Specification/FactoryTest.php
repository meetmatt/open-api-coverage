<?php

declare(strict_types=1);

namespace MeetMatt\OpenApiSpecCoverage\Test\Suite\Integration\Specification;

use MeetMatt\OpenApiSpecCoverage\OpenApi\OpenApiFactory;
use MeetMatt\OpenApiSpecCoverage\OpenApi\OpenApiReader;
use MeetMatt\OpenApiSpecCoverage\OpenApi\OpenApiSchemaParser;
use MeetMatt\OpenApiSpecCoverage\OpenApi\OpenApiSpecificationParser;
use MeetMatt\OpenApiSpecCoverage\Specification\Specification;
use PHPUnit\Framework\TestCase;

class FactoryTest extends TestCase
{
    private Specification $specification;

    protected function setUp(): void
    {
        $factory = new OpenApiFactory(
            new OpenApiReader(),
            new OpenApiSpecificationParser(
                new OpenApiSchemaParser()
            )
        );

        $this->specification = $factory->fromFile(codecept_data_dir('petstore.yaml'));
    }

    public function testFindPath(): void
    {
        $path = $this->specification->findPath('/pets/1234');

        $this->assertEquals('/pets/{id}', $path->getUriPath());
        $this->assertTrue($path->matches('/pets/1234'));
    }
}
