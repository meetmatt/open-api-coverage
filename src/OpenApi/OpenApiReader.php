<?php

declare(strict_types=1);

namespace MeetMatt\OpenApiSpecCoverage\OpenApi;

use cebe\openapi\Reader;
use cebe\openapi\ReferenceContext;
use cebe\openapi\spec\OpenApi;
use InvalidArgumentException;

class OpenApiReader
{
    public function readFromFile(string $specFile): OpenApi
    {
        if (!file_exists($specFile)) {
            throw new InvalidArgumentException("File doesn't exist: $specFile");
        }

        $type = strtolower(pathinfo($specFile, PATHINFO_EXTENSION));

        switch ($type) {
            case 'yml':
            case 'yaml':
                return Reader::readFromYamlFile($specFile, OpenApi::class, ReferenceContext::RESOLVE_MODE_ALL);

            case 'json':
                return Reader::readFromJsonFile($specFile, OpenApi::class, ReferenceContext::RESOLVE_MODE_ALL);

            default:
                throw new InvalidArgumentException("Unsupported spec format: $type. Supported formats: yml/yaml, json.");
        }
    }
}