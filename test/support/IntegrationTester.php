<?php

declare(strict_types=1);

namespace MeetMatt\OpenApiSpecCoverage\Test\Support;

use Codeception\Actor;
use Codeception\Module\Asserts;

/**
 * @mixin Asserts
 */
class IntegrationTester extends Actor
{
    use _generated\IntegrationTesterActions;
}
