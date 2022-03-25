<?php

declare(strict_types=1);

namespace MeetMatt\OpenApiSpecCoverage\Test\Suite\Integration\Coverage;

use MeetMatt\OpenApiSpecCoverage\Test\Support\CoverageTestCase;

class QueryStringCoverageTest extends CoverageTestCase
{
    public function testQueryStringCoverage(): void
    {
        $params = [
            [
                'String'      => 'one',
                'Number'      => 1.1,
                'Integer'     => 1,
                'EnumString'  => 'one',
                'EnumNumber'  => 1.1,
                'EnumInteger' => 1,
            ],
            [
                'String'      => 'two',
                'Number'      => 2.2,
                'Integer'     => 2,
                'EnumString'  => 'two',
                'EnumNumber'  => 2.2,
                'EnumInteger' => 2,
            ],
            [
                'String'      => 'three',
                'Number'      => 3.3,
                'Integer'     => 3,
                'EnumString'  => 'three',
                'EnumNumber'  => 3.3,
                'EnumInteger' => 3,
            ],
            [
                'String'              => 'four',
                'Number'              => 4.4,
                'Integer'             => 4,
                'EnumString'          => 'four',
                'EnumNumber'          => 4.4,
                'EnumInteger'         => 4,
                'UndocumentedString'  => 'five',
                'UndocumentedNumber'  => 5.5,
                'UndocumentedInteger' => 5,
            ],
        ];

        foreach ($params as $queryParams) {
            $this->recordHttpCall('get', 'http://server/resource', 200, $queryParams);
        }

        $spec = $this->coverage->process($this->container->getSpecFile('query.yaml'), $this->recorder);

        $path = $spec->path('/resource');
        $this->assertNotNull($path);

        $get = $path->operation('get');
        $this->assertNotNull($get);

        $this->printer->print($spec);
    }
}
