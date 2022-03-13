<?php

declare(strict_types=1);

namespace MeetMatt\OpenApiSpecCoverage\Test\Suite\Unit\Specification;

use Codeception\PHPUnit\TestCase;
use MeetMatt\OpenApiSpecCoverage\Specification\Operation;
use MeetMatt\OpenApiSpecCoverage\Specification\Parameter;
use MeetMatt\OpenApiSpecCoverage\Specification\Path;
use MeetMatt\OpenApiSpecCoverage\Specification\TypeEnum;
use MeetMatt\OpenApiSpecCoverage\Specification\TypeScalar;

class PathTest extends TestCase
{
    private Path $path;

    protected function setUp(): void
    {
        $this->path = new Path('/v1/users/{id}/name/{name}/{type}');

        $operation = $this->path->addOperation('get');

        $idParameter = $this->createMock(Parameter::class);
        $nameParameter = $this->createMock(Parameter::class);
        $typeParameter = $this->createMock(Parameter::class);

        $operation->addPathParameter($idParameter);
        $operation->addPathParameter($nameParameter);
        $operation->addPathParameter($typeParameter);

        $integerType = $this->createMock(TypeScalar::class);
        $integerType->method('asRegex')->willReturn('\d+');
        $idParameter->method('getName')->willReturn('id');
        $idParameter->method('getType')->willReturn($integerType);

        $stringType = $this->createMock(TypeScalar::class);
        $stringType->method('asRegex')->willReturn('[^\/.]+');
        $nameParameter->method('getName')->willReturn('name');
        $nameParameter->method('getType')->willReturn($stringType);

        $enumType = $this->createMock(TypeEnum::class);
        $enumType->method('asRegex')->willReturn('(foo|bar)');
        $typeParameter->method('getName')->willReturn('type');
        $typeParameter->method('getType')->willReturn($enumType);
    }

    public function testMatches(): void
    {
        $this->assertTrue($this->path->matches('/v1/users/123/name/john/foo'));
        $this->assertTrue($this->path->matches('/v1/users/123/name/john/bar'));
        $this->assertTrue($this->path->matches('/v1/users/123/name/john-doe/bar'));

        $this->assertFalse($this->path->matches('/v1/users/not-a-num/name/john/foo'));
        $this->assertFalse($this->path->matches('/v1/users/123/name/./bar'));
        $this->assertFalse($this->path->matches('/v1/users/123/name///bar'));
        $this->assertFalse($this->path->matches('/v1/users/123/name/../john/bar'));
        $this->assertFalse($this->path->matches('/v1/users/123/name/john/buz'));
        $this->assertFalse($this->path->matches('/v1/users/123/name/john'));
    }
}
