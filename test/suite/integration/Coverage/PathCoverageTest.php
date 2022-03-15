<?php

declare(strict_types=1);

namespace MeetMatt\OpenApiSpecCoverage\Test\Suite\Integration\Coverage;

use MeetMatt\OpenApiSpecCoverage\Test\Support\CoverageTestCase;

class PathCoverageTest extends CoverageTestCase
{
    public function testPathCoverage(): void
    {
        $params = [
            'blue'         => [
                'tags' => [
                    'funny',
                    'cute',
                ],
            ],
            'green'        => [
                'tags'  => [
                    'undocumented',
                ],
                'limit' => 100,
            ],
            'undocumented' => [
                'filter' => [
                    'name'         => 'Kitty',
                    'age'          => 5,
                    'undocumented' => 'black',
                ],
            ],
        ];

        foreach ($params as $pathParam => $queryParams) {
            $this->recordHttpCall('get', 'http://server/pets/' . $pathParam, 200, $queryParams);
        }

        $spec = $this->coverage->process($this->container->specFileGet(), $this->recorder);

        $pets = $spec->path('/pets/{type}');
        $this->assertNotNull($pets);

        $getPets = $pets->operation('get');
        $this->assertNotNull($getPets);

        $typeParam = $getPets->findPathParameters('type');
        $this->assertCount(1, $typeParam);
        $this->assertDocumented($typeParam[0]);
        $this->assertExecuted($typeParam[0]);

        $undocumented = $spec->path('/pets/undocumented');
        $this->assertNotNull($undocumented);
        $this->assertNotDocumented($undocumented);
        $this->assertExecuted($undocumented);
        $this->assertEmpty($undocumented->operation('get')->getPathParameters());

        // TODO: add more asserts

        $this->printer->print($spec);
    }
}
