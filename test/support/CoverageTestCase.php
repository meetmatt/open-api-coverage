<?php

declare(strict_types=1);

namespace MeetMatt\OpenApiSpecCoverage\Test\Support;

use Codeception\PHPUnit\TestCase;
use GuzzleHttp\Psr7\Response;
use GuzzleHttp\Psr7\ServerRequest;
use MeetMatt\OpenApiSpecCoverage\Coverage;
use MeetMatt\OpenApiSpecCoverage\Specification\CoverageElement;
use MeetMatt\OpenApiSpecCoverage\Specification\Operation;
use MeetMatt\OpenApiSpecCoverage\Specification\Parameter;
use MeetMatt\OpenApiSpecCoverage\Specification\Printer;
use MeetMatt\OpenApiSpecCoverage\Specification\Typed;
use MeetMatt\OpenApiSpecCoverage\Specification\TypeEnum;
use MeetMatt\OpenApiSpecCoverage\Specification\TypeScalar;
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
        $this->assertTrue($element->isDocumented(), sprintf('Expected %s to be marked as documented', get_class($element)));
    }

    protected function assertExecuted(CoverageElement $element): void
    {
        $this->assertTrue($element->isExecuted(), sprintf('Expected %s to be marked as executed', get_class($element)));
    }

    protected function assertNotDocumented(CoverageElement $element): void
    {
        $this->assertFalse($element->isDocumented(), sprintf('Expected %s to be marked as not documented', get_class($element)));
    }

    protected function assertNotExecuted(CoverageElement $element): void
    {
        $this->assertFalse($element->isExecuted(), sprintf('Expected %s to be marked as not executed', get_class($element)));
    }

    protected function assertDocumentedRecursive(CoverageElement $element): void
    {
        $this->assertDocumented($element);
        if ($element instanceof Typed && $element->getType() instanceof CoverageElement) {
            $this->assertDocumentedRecursive($element->getType());
        }
    }

    protected function assertExecutedRecursive(CoverageElement $element): void
    {
        $this->assertExecuted($element);
        if ($element instanceof Typed && $element->getType() instanceof CoverageElement) {
            $this->assertExecutedRecursive($element->getType());
        }
    }

    protected function assertNotDocumentedRecursive(CoverageElement $element): void
    {
        $this->assertNotDocumented($element);
        if ($element instanceof Typed && $element->getType() instanceof CoverageElement) {
            $this->assertNotDocumentedRecursive($element->getType());
        }
    }

    protected function assertNotExecutedRecursive(CoverageElement $element): void
    {
        $this->assertNotExecuted($element);
        if ($element instanceof Typed && $element->getType() instanceof CoverageElement) {
            $this->assertNotExecutedRecursive($element->getType());
        }
    }

    protected function assertPathParameter(Operation $operation, string $paramName): Parameter
    {
        $params = $operation->findPathParameters($paramName);

        $this->assertCount(1, $params);

        return current($params);
    }

    protected function assertQueryParameter(Operation $operation, string $paramName, bool $isDocumented = null, bool $isExecuted = null): Parameter
    {
        $param = $operation->findQueryParameter($paramName);

        if ($isDocumented !== null) {
            $this->assertEquals($isDocumented, $param->isDocumented());
        }
        if ($isExecuted !== null) {
            $this->assertEquals($isExecuted, $param->isExecuted());
        }

        return $param;
    }

    protected function assertScalarPathParameter(Operation $operation, string $paramName): void
    {
        $param = $this->assertPathParameter($operation, $paramName);

        /** @var TypeScalar $paramType */
        $paramType = $param->getType();
        $this->assertExecuted($paramType);
        $this->assertDocumented($paramType);
    }

    protected function assertEnumPathParameter(Operation $operation, string $paramName, array $documented, array $uncovered): void
    {
        $param = $this->assertPathParameter($operation, $paramName);

        /** @var TypeEnum $paramType */
        $paramType = $param->getType();
        $this->assertExecuted($paramType);
        $this->assertDocumented($paramType);

        $this->assertEquals($documented, $paramType->getDocumentedExecutedEnum());
        $this->assertEquals($uncovered, $paramType->getNotExecutedEnum());
        $this->assertEmpty($paramType->getUndocumentedEnum());
    }

    protected function assertEnumQueryParameter(Operation $operation, string $paramName, array $documented, array $uncovered): void
    {
        $param = $this->assertQueryParameter($operation, $paramName);

        /** @var TypeEnum $paramType */
        $paramType = $param->getType();
        $this->assertExecuted($paramType);
        $this->assertDocumented($paramType);

        $this->assertEquals($documented, $paramType->getDocumentedExecutedEnum());
        $this->assertEquals($uncovered, $paramType->getNotExecutedEnum());
        $this->assertEmpty($paramType->getUndocumentedEnum());
    }

    protected function recordHttpCall(
        string $method,
        string $uri,
        int $statusCode = 200,
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
