<?php

declare(strict_types=1);

namespace MeetMatt\OpenApiSpecCoverage;

use MeetMatt\OpenApiSpecCoverage\Specification\SpecificationFactoryInterface;
use MeetMatt\OpenApiSpecCoverage\TestRecorder\HttpCall;
use MeetMatt\OpenApiSpecCoverage\TestRecorder\TestRecorder;

class Coverage
{
    private SpecificationFactoryInterface $factory;

    public function __construct(SpecificationFactoryInterface $factory)
    {
        $this->factory = $factory;
    }

    /**
     * Generates expected Specification by parsing the specification file
     * Generates actual Specification by parsing the test recorder logs
     * Compares the specification to the http and assertion logs
     * Fills out the input and output criteria
     * Calculates the TCL
     */
    public function process(string $specFile, TestRecorder $testRecorder): void
    {
        // 1. Parse specification.
        $specification = $this->factory->fromFile($specFile);

        $operation     = null;
        foreach ($testRecorder->getLogs() as $log) {
            // 2. On each HTTP call (REST Module) find the path and operation in the spec, mark it as documented and passed.
            if ($log instanceof HttpCall) {
                $request = $log->getRequest();
                $path    = $specification->findPath($request->getUri()->getPath());
                if ($path === null) {
                    // undocumented path
                    // If there's no path, then log an undocumented path (plus operation, parameters (infer path parameters), query string parameters, request body contents).
                    // TODO: decide where to store the undocumented specification elements
                    continue;
                }

                $operation = $path->findOperation($request->getMethod());
                if ($operation === null) {
                    // undocumented operation - mark the path as called, but not covered, and with undocumented operation
                    continue;
                }
            }

            if ($operation === null) {
                // assertion was called without a prior API call
                continue;
            }

            // Request parameters - path parameters
            $pathParameters = $operation->getPathParameters();
            if (!empty($pathParameters)) {
                // go through each path parameter and mark as covered
            }

            // Dig deeper to the request parameters: query parameters.

        }
    }
}
