<?php

declare(strict_types=1);

namespace MeetMatt\OpenApiSpecCoverage\Test\Suite\Integration;

use GuzzleHttp\Psr7\Response;
use GuzzleHttp\Psr7\ServerRequest;
use GuzzleHttp\Psr7\Utils;
use MeetMatt\OpenApiSpecCoverage\Coverage;
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
        $this->recordHttpCall('post', 'http://server/pets', 200, ['pet' => 'test']);
        $this->recordHttpCall('post', 'http://server/undocumented', 200, ['pet' => 'test']);

        $specification = $this->coverage->process($this->container->specFile(), $this->recorder);

        $this->assertTrue($specification->findPath('/pets')->isDocumented());
        $this->assertTrue($specification->findPath('/pets/{id}')->isDocumented());
        $this->assertFalse($specification->findPath('/pets/{id}')->isExecuted());
        $this->assertFalse($specification->findPath('/undocumented')->isDocumented());
    }

    protected function recordHttpCall(string $method, string $uri, int $statusCode, array $content = null): void
    {
        $contentType = 'application/json';
        $headers     = ['Content-type' => $contentType, 'Accept' => $contentType];

        $this->recorder->recordHttpCall(
            new ServerRequest($method, $uri, $headers, $content ? Utils::streamFor(json_encode($content)) : null),
            new Response($statusCode)
        );
    }
}
