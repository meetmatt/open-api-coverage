<?php

declare(strict_types=1);

namespace MeetMatt\OpenApiSpecCoverage\TestRecorder;

class ResponseContentAssertion
{
    /**
     * @var string|array
     */
    private $content;

    public function __construct($content)
    {
        $this->content = $content;
    }

    /**
     * @return array|string
     */
    public function getContent()
    {
        return $this->content;
    }
}
