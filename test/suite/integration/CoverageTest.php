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

    public function testProcess(): void
    {
        $params = [
            [['type' => 'blue'], ['tags' => ['funny']]],
            [['type' => 'blue'], ['tags' => ['cute']]],
            [['type' => 'blue'], ['tags' => ['undocumented']]],
            [['type' => 'green'], ['limit' => 100]],
            [
                ['type' => 'undocumented'],
                ['filter' => [
                    'name'         => 'Kitty',
                    'age'          => 5,
                    'undocumented' => 'black',
                ]]
            ],
        ];

        foreach ($params as $param) {
            $this->recordHttpCall('get', 'http://server/pets/' . $param[0]['type'], 200, $param[1]);
        }

        $spec = $this->coverage->process($this->container->specFileSimple(), $this->recorder);

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

        $request = (new ServerRequest(
            $method, $uri, $headers, $content ? Utils::streamFor(json_encode($content)) : null
        ))->withQueryParams($queryParams);

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
                        'negate' => 0
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
                        'negate' => 1
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
                        'negate' => 3
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
