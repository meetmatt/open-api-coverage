<?php

declare(strict_types=1);

namespace MeetMatt\OpenApiSpecCoverage\Test\Suite\Integration;

use MeetMatt\OpenApiSpecCoverage\Coverage;
use MeetMatt\OpenApiSpecCoverage\OpenApi\OpenApiFactory;
use MeetMatt\OpenApiSpecCoverage\OpenApi\OpenApiReader;
use MeetMatt\OpenApiSpecCoverage\OpenApi\OpenApiSchemaParser;
use MeetMatt\OpenApiSpecCoverage\OpenApi\OpenApiSpecificationParser;
use MeetMatt\OpenApiSpecCoverage\Test\Support\IntegrationTester;
use MeetMatt\OpenApiSpecCoverage\TestRecorder\TestRecorder;

class CoverageCest
{
    public function testCompare(IntegrationTester $I): void
    {
        $factory = new OpenApiFactory(
            new OpenApiReader(),
            new OpenApiSpecificationParser(
                new OpenApiSchemaParser()
            )
        );

        $coverage     = new Coverage($factory);
        $testRecorder = new TestRecorder();

        $coverage->compare(codecept_data_dir('petstore.yaml'), $testRecorder);
    }
}
