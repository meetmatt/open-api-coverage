<?php

declare(strict_types=1);

namespace MeetMatt\OpenApiSpecCoverage\TestRecorder;

interface TestRecorder
{
    /**
     * HTTP call made
     *   -> input criteria - path called
     *   -> input criteria - operation called
     *   -> input criteria - content type
     *   -> input criteria - passed path parameters
     *   -> input criteria - passed query parameters
     *   -> input criteria - passed request body
     *   -> input criteria - passed content type (implicitly with json_encode also counts?)
     *
     * @param Request  $request
     * @param Response $response
     */
    public function httpCall(Request $request, Response $response): void;

    // content type asserted
    //   -> output criteria - content type (implicitly with json_decode also counts)
    public function contentTypeAsserted(string $contentType): void;

    // status code asserted
    //   -> output criteria - status code class
    //   -> output criteria - status code
    public function statusCodeAsserted(string $statusCode): void;

    // response content asserted
    //   -> output criteria - response content properties
    public function responseContentAsserted($content): void;
}