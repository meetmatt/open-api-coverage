<?php

declare(strict_types=1);

namespace MeetMatt\OpenApiSpecCoverage\Test\Suite\Integration\Coverage;

use MeetMatt\OpenApiSpecCoverage\Test\Support\CoverageTestCase;

class PathCoverageTest extends CoverageTestCase
{
    public function testPathCoverage(): void
    {
        $params = [
            [
                'stringEnum'  => 'blue',
                'numberEnum'  => 3.14,
                'integerEnum' => 1,
                'string'      => 'what',
                'number'      => 0.1234,
                'integer'     => 50,
            ],
            [
                'stringEnum'  => 'green',
                'numberEnum'  => 2.71,
                'integerEnum' => 2,
                'string'      => 'where',
                'number'      => 45.67,
                'integer'     => 100,
            ],
            [
                'stringEnum'  => 'undocumented',
                'numberEnum'  => 0.01,
                'integerEnum' => 100,
                'string'      => 'whatever',
                'number'      => 789,
                'integer'     => 200,
            ],
        ];

        foreach ($params as $param) {
            $path = implode('/', [
                $param['stringEnum'],
                $param['numberEnum'],
                $param['integerEnum'],
                $param['string'],
                $param['number'],
                $param['integer'],
            ]);

            $uri = 'http://server/resource/' . $path;

            $this->recordHttpCall('get', $uri);
        }

        $spec = $this->coverage->process($this->container->getSpecFile('path.yaml'), $this->recorder);

        $paths = $spec->getPaths();
        $this->assertCount(2, $paths);

        $pets = $spec->path('/resource/{stringEnum}/{numberEnum}/{integerEnum}/{string}/{number}/{integer}');
        $this->assertNotNull($pets);
        $this->assertSame($pets, $paths[0]);

        $getPets = $pets->operation('get');
        $this->assertNotNull($getPets);

        $stringEnum = $getPets->findPathParameters('stringEnum');
        $this->assertCount(1, $stringEnum);
        $stringEnum = current($stringEnum);
        $this->assertDocumented($stringEnum);
        $this->assertExecuted($stringEnum);

        $numberEnum = $getPets->findPathParameters('numberEnum');
        $this->assertCount(1, $numberEnum);
        $numberEnum = current($numberEnum);
        $this->assertDocumented($numberEnum);
        $this->assertExecuted($numberEnum);

        $integerEnum = $getPets->findPathParameters('integerEnum');
        $this->assertCount(1, $integerEnum);
        $integerEnum = current($integerEnum);
        $this->assertDocumented($integerEnum);
        $this->assertExecuted($integerEnum);

        $string = $getPets->findPathParameters('string');
        $this->assertCount(1, $string);
        $string = current($string);
        $this->assertDocumented($string);
        $this->assertExecuted($string);

        $number = $getPets->findPathParameters('number');
        $this->assertCount(1, $number);
        $number = current($number);
        $this->assertDocumented($number);
        $this->assertExecuted($number);

        $integer = $getPets->findPathParameters('integer');
        $this->assertCount(1, $integer);
        $integer = current($integer);
        $this->assertDocumented($integer);
        $this->assertExecuted($integer);


        $undocumented = $spec->path('/resource/undocumented/0.01/100/whatever/789/200');
        $this->assertNotNull($undocumented);
        $this->assertNotDocumented($undocumented);
        $this->assertExecuted($undocumented);
        $this->assertEmpty($undocumented->operation('get')->getPathParameters());

        $this->printer->print($spec);
        // TODO: add more asserts

        /*─────────────┬─────────────┬────────────────────────┬──────────────────────────────────────┬────────────┬──────────┐
        │ Path         │ HTTP Method │ Element                │ Type                                 │ Documented │ Executed │
        ├──────────────┼─────────────┼────────────────────────┼──────────────────────────────────────┼────────────┼──────────┤
        │ /pets/{type} │ get         │ path.type              │ <string>['blue','green']             │ +          │ +        │
        │              │             │ path.type              │ <string>['uncovered']                │ +          │ -        │
        │              │             │ query.tags[]           │ <string>['funny','cute','uncovered'] │ +          │ -        │
        │              │             │ query.filter.name      │ <string>                             │ +          │ -        │
        │              │             │ query.filter.age       │ <number>                             │ +          │ -        │
        │              │             │ query.filter.uncovered │ <string>                             │ +          │ -        │
        │              │             │ query.limit            │ <integer>                            │ +          │ -        │
        │              │             │ query.uncovered        │ <string>                             │ +          │ -        │
        └──────────────┴─────────────┴────────────────────────┴──────────────────────────────────────┴────────────┴──────────*/


    }
}
