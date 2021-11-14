<?php

namespace MeetMatt\OpenApiSpecCoverage;

use MeetMatt\OpenApiSpecCoverage\Specification\Specification;

class Coverage
{
    /** @var Specification */
    private $spec;

    /** @var InputCriteria */
    private $input;

    /** @var OutputCriteria */
    private $output;
}