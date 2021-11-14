<?php

namespace MeetMatt\OpenApiSpecCoverage\Specification;

class Property
{
    /** @var string */
    private $path;

    /** @var string[]|null */
    private $values;

    /**
     * @param string $path
     * @param string[]|null $values
     */
    public function __construct($path, $values = null)
    {
        $this->path   = $path;
        $this->values = $values;
    }

    /**
     * @return string
     */
    public function getPath()
    {
        return $this->path;
    }

    /**
     * @return string[]|null
     */
    public function getValues()
    {
        return $this->values;
    }
}