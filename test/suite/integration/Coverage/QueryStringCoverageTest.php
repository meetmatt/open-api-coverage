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
                'tags' => [
                    'funny',
                    'cute',
                ],
            ],
            [
                'tags'  => [
                    'undocumented',
                ],
                'limit' => 100,
            ],
            [
                'filter' => [
                    'name'         => 'Kitty',
                    'age'          => 5,
                    'undocumented' => 'black',
                ],
            ],
        ];

        foreach ($params as $queryParams) {
            $this->recordHttpCall('get', 'http://server/pets', 200, $queryParams);
        }

        $spec = $this->coverage->process($this->container->getSpecFile('petstore_get.yaml'), $this->recorder);

        $path = $spec->path('/pets');
        $get  = $path->operation('get');

        $this->assertNotNull($path);
        $this->assertNotNull($get);
        $this->assertNotEmpty($get->getQueryParameters());
        $this->assertNotNull($get->findQueryParameter('tags'));
        $this->assertNotNull($get->findQueryParameter('limit'));
        $this->assertNotNull($get->findQueryParameter('filter'));

        // TODO: add more asserts

        $this->printer->print($spec);
    }

    private function getQueryParameters(): array
    {
        return [
            // name: tags[], style: form, explode: true
            // type: array, items: type: string, enum: [funny, sleepy, cute]
            'tags'     => [
                0 => 'funny',
                1 => 'sleepy',
                2 => 'cute',
                3 => 'undocumented',
            ],
            // alternative:
            // 'tags'   => [
            //     'funny',
            //     'sleepy',
            //     'cute',
            //     'undocumented',
            // ],
            'family'   => [
                'cat',
                'dog',
                'undocumented',
            ],
            'criteria' => [
                [
                    'field'           => 'name',
                    'op'              => [
                        'type'   => 'eq',
                        'negate' => 0,
                    ],
                    'value'           => '',
                    'listPropEnum'    => [
                        'first',
                        'second',
                    ],
                    'listPropNumbers' => [1, 2, 3],
                ],
                [
                    'field'           => 'family',
                    'op'              => [
                        'type'   => 'like',
                        'negate' => 1,
                    ],
                    'value'           => '',
                    'undocumented'    => 99,
                    'listPropEnum'    => [
                        'first',
                        'undocumented',
                    ],
                    'listPropNumbers' => [],
                ],
                [
                    'field'           => 'undocumented',
                    'op'              => [
                        'type'   => 'undocumented',
                        'negate' => 3,
                    ],
                    'value'           => '',
                    'undocumented'    => 99,
                    'listPropEnum'    => [
                        'first',
                        'undocumented',
                    ],
                    'listPropNumbers' => [],
                ],
            ],
            'object'   => [
                'firstKey'     => 'qwerty',
                'secondKey'    => 'qwerty',
                'undocumented' => 'qwerty',
            ],
            'limit'    => 100,
            // uncovered
        ];
    }
}
