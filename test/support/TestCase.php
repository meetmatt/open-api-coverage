<?php

declare(strict_types=1);

namespace MeetMatt\OpenApiSpecCoverage\Test\Support;

class TestCase extends \Codeception\PHPUnit\TestCase
{
    protected Container $container;

    public function __construct(?string $name = null, array $data = [], $dataName = '')
    {
        parent::__construct($name, $data, $dataName);

        $this->container = new Container();
    }
}
