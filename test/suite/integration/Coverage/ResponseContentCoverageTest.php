<?php

declare(strict_types=1);

namespace MeetMatt\OpenApiSpecCoverage\Test\Suite\Integration\Coverage;

use MeetMatt\OpenApiSpecCoverage\Test\Support\CoverageTestCase;

/*
200:
  content:
    application/json:
      schema:
        $ref: '#/components/schemas/Object'
404:
  content:
    text/plain:
      schema:
        type: string
 */

class ResponseContentCoverageTest extends CoverageTestCase
{
    // test status code -> content type coverage
    // test status code -> content type -> schema coverage

    public function testStatusCodeCoverage(): void
    {
        $this->recordHttpCall('get', 'http://server/resource');
        $this->recordStatusCodeAsserted('200');

        $spec = $this->coverage->process($this->container->getSpecFile('response.yaml'), $this->recorder);

        $path      = $spec->path('/resource');
        $get       = $path->operation('get');
        $responses = $get->getResponses();
        foreach ($responses as $response) {
            $this->assertDocumented($response);
            $this->assertExecuted($response);
            $this->assertAsserted($response);
        }

        $this->print($spec);
    }
}
