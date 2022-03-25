<?php

declare(strict_types=1);

namespace MeetMatt\OpenApiSpecCoverage\Test\Suite\Integration\Coverage;

use MeetMatt\OpenApiSpecCoverage\Test\Support\CoverageTestCase;

class PathCoverageTest extends CoverageTestCase
{
    public function testPathCoverage(): void
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

        foreach ($params as $param) {
            $this->recordHttpCall('get', 'http://server/resource/' . implode('/', array_values($param)));
        }

        $spec = $this->coverage->process($this->container->getSpecFile('path.yaml'), $this->recorder);

        $paths = $spec->getPaths();
        $this->assertCount(3, $paths);

        $path = $spec->path('/resource/{String}/{Number}/{Integer}/{EnumString}/{EnumNumber}/{EnumInteger}');
        $this->assertNotNull($path);
        $this->assertSame($path, $paths[0]);

        $get = $path->operation('get');
        $this->assertNotNull($get);

        $this->assertScalarPathParameter($get, 'String');
        $this->assertScalarPathParameter($get, 'Number');
        $this->assertScalarPathParameter($get, 'Integer');

        $this->assertEnumPathParameter($get, 'EnumString', ['one', 'two'], ['uncovered']);
        $this->assertEnumPathParameter($get, 'EnumNumber', [1.1, 2.2], [9.9]);
        $this->assertEnumPathParameter($get, 'EnumInteger', [1, 2], [9]);

        $undocumented = $spec->path('/resource/three/3.3/3/three/3.3/3');
        $this->assertExecuted($undocumented);
        $this->assertNotDocumented($undocumented);

        $undocumented = $spec->path('/resource/four/4.4/4/four/4.4/4/five/5.5/5');
        $this->assertExecuted($undocumented);
        $this->assertNotDocumented($undocumented);
    }
}
