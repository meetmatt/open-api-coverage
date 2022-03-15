<?php

declare(strict_types=1);

namespace MeetMatt\OpenApiSpecCoverage\Test\Support;

use Codeception\PHPUnit\TestCase;
use GuzzleHttp\Psr7\Response;
use GuzzleHttp\Psr7\ServerRequest;
use MeetMatt\OpenApiSpecCoverage\Coverage;
use MeetMatt\OpenApiSpecCoverage\Specification\CoverageElement;
use MeetMatt\OpenApiSpecCoverage\Specification\Printer;
use MeetMatt\OpenApiSpecCoverage\TestRecorder\TestRecorder;

class CoverageTestCase extends TestCase
{
    protected Container    $container;

    protected Coverage     $coverage;

    protected TestRecorder $recorder;

    protected Printer      $printer;

    public function __construct(?string $name = null, array $data = [], $dataName = '')
    {
        parent::__construct($name, $data, $dataName);

        $this->container = new Container();
    }

    protected function setUp(): void
    {
        $this->recorder = new TestRecorder();
        $this->coverage = new Coverage($this->container->factory());
        $this->printer  = new Printer();
    }

    protected function assertDocumented(CoverageElement $element): void
    {
        $this->assertTrue($element->isDocumented());
    }

    protected function assertExecuted(CoverageElement $element): void
    {
        $this->assertTrue($element->isExecuted());
    }

    protected function assertNotDocumented(CoverageElement $element): void
    {
        $this->assertFalse($element->isDocumented());
    }

    protected function assertNotExecuted(CoverageElement $element): void
    {
        $this->assertFalse($element->isExecuted());
    }

    protected function recordHttpCall(
        string $method,
        string $uri,
        int $statusCode,
        array $queryParams = [],
        array $content = null,
        array $headers = []
    ): void {
        $contentType = 'application/json';
        $headers     = !empty($headers)
            ? $headers
            : [
                'Content-type' => $contentType,
                'Accept'       => $contentType,
            ];

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
}
