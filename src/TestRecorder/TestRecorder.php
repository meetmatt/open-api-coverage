<?php

declare(strict_types=1);

namespace MeetMatt\OpenApiSpecCoverage\TestRecorder;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class TestRecorder
{
    /** @var HttpCall[]|ContentTypeAssertion[]|StatusCodeAssertion[]|ResponseContentAssertion[] */
    private array $log;

    public function recordHttpCall(ServerRequestInterface $request, ResponseInterface $response): void
    {
        $this->log[] = new HttpCall($request, $response);
    }

    public function contentTypeAsserted(string $contentType): void
    {
        $this->log[] = new ContentTypeAssertion($contentType);
    }

    public function statusCodeAsserted(string $statusCode): void
    {
        $this->log[] = new StatusCodeAssertion($statusCode);
    }

    public function responseContentAsserted($content): void
    {
        $this->log[] = new ResponseContentAssertion($content);
    }

    /**
     * @return ContentTypeAssertion[]|HttpCall[]|ResponseContentAssertion[]|StatusCodeAssertion[]
     */
    public function getLogs(): array
    {
        return $this->log;
    }
}
