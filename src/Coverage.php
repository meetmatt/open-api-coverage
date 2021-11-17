<?php

namespace MeetMatt\OpenApiSpecCoverage;

use Exception;
use MeetMatt\OpenApiSpecCoverage\Specification\Factory;
use MeetMatt\OpenApiSpecCoverage\Specification\Specification;

class Coverage
{
    /** @var Specification */
    private $spec;

    /** @var InputCriteria */
    private $input;

    /** @var OutputCriteria */
    private $output;

    /**
     * @param string $specFile
     *
     * @throws Exception
     */
    public function __construct($specFile)
    {
        $this->spec = Factory::fromFile($specFile);
    }

    /**
     * @return Specification
     */
    public function spec()
    {
        return $this->spec;
    }
}