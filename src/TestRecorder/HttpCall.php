<?php

declare(strict_types=1);

namespace MeetMatt\OpenApiSpecCoverage\TestRecorder;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class HttpCall
{
    private ServerRequestInterface $request;

    private ResponseInterface $response;

    public function __construct(ServerRequestInterface $request, ResponseInterface $response)
    {
        $this->request  = $request;
        $this->response = $response;
    }

    public function getRequest(): ServerRequestInterface
    {
        return $this->request;
    }

    public function getResponse(): ResponseInterface
    {
        return $this->response;
    }
}
