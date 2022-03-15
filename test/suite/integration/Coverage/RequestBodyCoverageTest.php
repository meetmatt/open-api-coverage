<?php

declare(strict_types=1);

namespace MeetMatt\OpenApiSpecCoverage\Test\Suite\Integration\Coverage;

use MeetMatt\OpenApiSpecCoverage\Test\Support\CoverageTestCase;

class RequestBodyCoverageTest extends CoverageTestCase
{
    public function testRequestBody(): void
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

        $spec = $this->coverage->process($this->container->getSpecFile('petstore_post.yaml'), $this->recorder);

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

        $this->printer->print($spec);
    }
}
