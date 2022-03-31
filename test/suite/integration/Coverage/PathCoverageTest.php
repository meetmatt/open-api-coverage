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
                'String'      => 'three',
                'Number'      => 3.3,
                'Integer'     => 3,
                'EnumString'  => 'three',
                'EnumNumber'  => 3.3,
                'EnumInteger' => 3,
            ],
        ];

        foreach ($params as $param) {
            $this->recordHttpCall('get', 'http://server/resource/' . implode('/', array_values($param)));
        }

        $spec = $this->coverage->process($this->container->getSpecFile('path.yaml'), $this->recorder);

        $paths = $spec->getPaths();
        $this->assertCount(2, $paths);

        $path = $spec->path('/resource/{String}/{Number}/{Integer}/{EnumString}/{EnumNumber}/{EnumInteger}');
        $this->assertNotNull($path);
        $this->assertSame($path, $paths[0]);

        $get = $path->operation('get');
        $this->assertNotNull($get);

        $this->assertScalarPathParameter($get, 'String');
        $this->assertScalarPathParameter($get, 'Number');
        $this->assertScalarPathParameter($get, 'Integer');

        $this->assertEnumPathParameter($get, 'EnumString', ['one'], ['two']);
        $this->assertEnumPathParameter($get, 'EnumNumber', [1.1], [2.2]);
        $this->assertEnumPathParameter($get, 'EnumInteger', [1], [2]);

        $undocumented = $spec->path('/resource/three/3.3/3/three/3.3/3');
        $this->assertExecuted($undocumented);
        $this->assertNotDocumented($undocumented);
    }
}
