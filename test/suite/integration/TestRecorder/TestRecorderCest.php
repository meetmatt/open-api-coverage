<?php

declare(strict_types=1);

namespace MeetMatt\OpenApiSpecCoverage\Test\Suite\Integration\TestRecorder;

use GuzzleHttp\Psr7\Response;
use GuzzleHttp\Psr7\ServerRequest;
use GuzzleHttp\Psr7\Uri;
use GuzzleHttp\Psr7\Utils;
use MeetMatt\OpenApiSpecCoverage\Test\Support\IntegrationTester;
use MeetMatt\OpenApiSpecCoverage\TestRecorder\ContentTypeAssertion;
use MeetMatt\OpenApiSpecCoverage\TestRecorder\HttpCall;
use MeetMatt\OpenApiSpecCoverage\TestRecorder\ResponseContentAssertion;
use MeetMatt\OpenApiSpecCoverage\TestRecorder\StatusCodeAssertion;
use MeetMatt\OpenApiSpecCoverage\TestRecorder\TestRecorder;

class TestRecorderCest
{
    public function testLogs(IntegrationTester $I): void
    {
        $recorder = new TestRecorder();

        $content  = json_encode(['pet' => 'test']);
        $body     = Utils::streamFor($content);
        $request  = new ServerRequest(
            'post',
            new Uri('http://server/pets'),
            [
                'Content-type' => 'application/json',
                'Accept'       => 'application/json',
            ],
            $body
        );
        $response = new Response(200);

        $recorder->recordHttpCall($request, $response);
        $recorder->statusCodeAsserted('200');
        $recorder->contentTypeAsserted('application/json');
        $recorder->responseContentAsserted(['pet' => 'test']);

        $logs = [
            new HttpCall($request, $response),
            new StatusCodeAssertion('200'),
            new ContentTypeAssertion('application/json'),
            new ResponseContentAssertion(['pet' => 'test']),
        ];

        $I->assertEquals($recorder->getLogs(), $logs);
    }
}
