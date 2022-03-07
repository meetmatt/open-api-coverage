<?php

declare(strict_types=1);

namespace MeetMatt\OpenApiSpecCoverage\TestRecorder;

class ResponseContentAssertion
{
    /** @var string|array */
    private $content;

    /**
     * @param string|array $content
     */
    public function __construct($content)
    {
        $this->content = $content;
    }

    /**
     * @return string|array
     */
    public function getContent()
    {
        return $this->content;
    }
}
