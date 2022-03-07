<?php

declare(strict_types=1);

namespace MeetMatt\OpenApiSpecCoverage\TestRecorder;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class TestRecorder
{
    /** @var HttpCall[]|ResponseContentTypeAssertion[]|ResponseStatusCodeAssertion[]|ResponseContentAssertion[] */
    private array $log;

    public function recordHttpCall(ServerRequestInterface $request, ResponseInterface $response): void
    {
        $this->log[] = new HttpCall($request, $response);
    }

    public function contentTypeAsserted(string $contentType): void
    {
        $this->log[] = new ResponseContentTypeAssertion($contentType);
    }

    public function statusCodeAsserted(string $statusCode): void
    {
        $this->log[] = new ResponseStatusCodeAssertion($statusCode);
    }

    public function responseContentAsserted($content): void
    {
        $this->log[] = new ResponseContentAssertion($content);
    }

    /**
     * @return ResponseContentTypeAssertion[]|HttpCall[]|ResponseContentAssertion[]|ResponseStatusCodeAssertion[]
     */
    public function getLogs(): array
    {
        return $this->log;
    }
}
