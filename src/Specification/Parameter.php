<?php

namespace MeetMatt\OpenApiSpecCoverage\Specification;

class Parameter
{
    /** @var string */
    private $name;

    /** @var string[]|null */
    private $values;

    /**
     * @param string $name
     * @param string[]|null $values
     */
    public function __construct($name, $values = null)
    {
        $this->name   = $name;
        $this->values = $values;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return string[]|null
     */
    public function getValues()
    {
        return $this->values;
    }
}