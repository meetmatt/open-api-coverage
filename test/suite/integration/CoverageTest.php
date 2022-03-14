<?php

declare(strict_types=1);

namespace MeetMatt\OpenApiSpecCoverage\Test\Suite\Integration;

use GuzzleHttp\Psr7\Response;
use GuzzleHttp\Psr7\ServerRequest;
use GuzzleHttp\Psr7\Utils;
use MeetMatt\OpenApiSpecCoverage\Coverage;
use MeetMatt\OpenApiSpecCoverage\Specification\Printer;
use MeetMatt\OpenApiSpecCoverage\Test\Support\TestCase;
use MeetMatt\OpenApiSpecCoverage\TestRecorder\TestRecorder;

class CoverageTest extends TestCase
{
    private Coverage $coverage;

    protected function setUp(): void
    {
        $factory = $this->container->factory();

        $this->coverage = new Coverage($factory);
        $this->recorder = new TestRecorder();
    }

    public function testProcessPathAndQueryParams(): void
    {
        $params = [
            [['type' => 'blue'], ['tags' => ['funny']]],
            [['type' => 'blue'], ['tags' => ['cute']]],
            [['type' => 'blue'], ['tags' => ['undocumented']]],
            [['type' => 'green'], ['limit' => 100]],
            [
                ['type' => 'undocumented'],
                [
                    'filter' => [
                        'name'         => 'Kitty',
                        'age'          => 5,
                        'undocumented' => 'black',
                    ],
                ],
            ],
        ];

        foreach ($params as $param) {
            $this->recordHttpCall('get', 'http://server/pets/' . $param[0]['type'], 200, $param[1]);
        }

        $spec = $this->coverage->process($this->container->specFileGet(), $this->recorder);

        // TODO: add more asserts

        echo "\n";
        ob_flush();
        (new Printer())->print($spec);
    }

    public function testProcessRequestBody(): void
    {
        $bodies = [
            [
                'name'         => 'Kitty',
                'family'       => 'cat',
                'tags'         => ['funny'],
                'undocumented' => 5,
            ],
            [
                'name'   => 'Fluffy',
                'family' => 'dog',
                'tags'   => [],
            ],
            [
                'name'   => 'Chirpy',
                'family' => 'undocumented',
                'tags'   => ['cute', 'undocumented'],
            ],
            // TODO: fix issue with comparing expected object vs list of objects / list of lists
            // [
            //     [
            //         'name'   => 'Nesty',
            //         'family' => 'cat',
            //         'tags'   => ['cute'],
            //     ]
            // ]
        ];

        foreach ($bodies as $body) {
            $this->recordHttpCall('post', 'http://server/pets', 201, [], $body);
        }

        $spec = $this->coverage->process($this->container->specFilePost(), $this->recorder);

        $path = $spec->path('/pets');
        $this->assertTrue($path->isDocumented());
        $this->assertTrue($path->isExecuted());

        $post = $path->operation('post');
        $this->assertTrue($post->isDocumented());
        $this->assertTrue($post->isExecuted());

        $requestBody = $post->findRequestBody('application/json');
        $this->assertNotNull($requestBody);
        $this->assertTrue($requestBody->isDocumented());

        $this->assertTrue($requestBody->isExecuted());

        // TODO: add more asserts

        echo "\n";
        ob_flush();
        (new Printer())->print($spec);
    }

    protected function recordHttpCall(
        string $method,
        string $uri,
        int $statusCode,
        array $queryParams = [],
        array $content = null
    ): void {
        $contentType = 'application/json';
        $headers     = ['Content-type' => $contentType, 'Accept' => $contentType];

        $request = new ServerRequest($method, $uri, $headers);
        if (!empty($queryParams)) {
            $request = $request->withQueryParams($queryParams);
        }
        if (!empty($content)) {
            $request = $request->withParsedBody($content);
        }
        $response = new Response($statusCode);

        $this->recorder->recordHttpCall($request, $response);
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
