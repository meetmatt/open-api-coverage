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

        return match ($type) {
            'yml', 'yaml' => Reader::readFromYamlFile($specFile, OpenApi::class, ReferenceContext::RESOLVE_MODE_ALL),
            'json' => Reader::readFromJsonFile($specFile, OpenApi::class, ReferenceContext::RESOLVE_MODE_ALL),
            default => throw new InvalidArgumentException("Unsupported spec format: $type. Supported formats: yml/yaml, json."),
        };
    }
}