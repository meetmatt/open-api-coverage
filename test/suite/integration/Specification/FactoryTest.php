<?php

declare(strict_types=1);

namespace MeetMatt\OpenApiSpecCoverage\Test\Suite\Integration\Specification;

use MeetMatt\OpenApiSpecCoverage\Specification\Specification;
use MeetMatt\OpenApiSpecCoverage\Test\Support\TestCase;

class FactoryTest extends TestCase
{
    private Specification $specification;

    protected function setUp(): void
    {
        $factory = $this->container->factory();

        $this->specification = $factory->fromFile($this->container->specFile());
    }

    public function testFindPath(): void
    {
        $path = $this->specification->findPath('/pets/1234');

        $this->assertEquals('/pets/{id}', $path->getUriPath());
        $this->assertTrue($path->matches('/pets/1234'));
    }
}
