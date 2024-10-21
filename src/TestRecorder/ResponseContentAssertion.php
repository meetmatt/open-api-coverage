<?php

declare(strict_types=1);

namespace MeetMatt\OpenApiSpecCoverage\TestRecorder;

class ResponseContentAssertion
{
    /**
     * @param string|array $content
     */
    public function __construct(private $content)
    {
    }

    /**
     * @return string|array
     */
    public function getContent()
    {
        return $this->content;
    }
}
