<?php

declare(strict_types=1);

namespace MeetMatt\OpenApiSpecCoverage\Test\Suite\Integration\Coverage;

use MeetMatt\OpenApiSpecCoverage\Specification\TypeArray;
use MeetMatt\OpenApiSpecCoverage\Specification\TypeEnum;
use MeetMatt\OpenApiSpecCoverage\Test\Support\CoverageTestCase;

class QueryParameterCoverageTest extends CoverageTestCase
{
    public function testQueryParameterCoverage(): void
    {
        $params = [
            'Documented scalars #1' => [
                'String'  => 'one',
                'Number'  => 1.1,
                'Integer' => 1,
            ],

            'Documented scalar enums #1' => [
                'EnumString'  => 'one',
                'EnumNumber'  => 1.1,
                'EnumInteger' => 1,
            ],

            'Documented scalar arrays' => [
                'ArrayString'  => ['one'],
                'ArrayNumber'  => [1.1],
                'ArrayInteger' => [1],
            ],

            'Documented array enums' => [
                'ArrayEnumString'  => ['one'],
                'ArrayEnumNumber'  => [1.1],
                'ArrayEnumInteger' => [1],
            ],

            'Undocumented enums' => [
                'EnumString'  => 'three',
                'EnumNumber'  => 3.3,
                'EnumInteger' => 3,
            ],

            'Undocumented array enums' => [
                'ArrayEnumString'  => ['three'],
                'ArrayEnumNumber'  => [3.3],
                'ArrayEnumInteger' => [3],
            ],

            /*
                        'Documented ArrayObject' => [
                            'ArrayObject' => [
                                [
                                    'String'           => 'one',
                                    'Number'           => 1.1,
                                    'Integer'          => 1,
                                    'EnumString'       => 'one',
                                    'EnumNumber'       => 1.1,
                                    'EnumInteger'      => 1,
                                    'ArrayString'      => ['one'],
                                    'ArrayNumber'      => [1.1],
                                    'ArrayInteger'     => [1],
                                    'ArrayEnumString'  => ['one'],
                                    'ArrayEnumNumber'  => [1.1],
                                    'ArrayEnumInteger' => [1],
                                ],
                            ],
                        ],

                        'Documented ArrayObjectPHP' => [
                            'ArrayObjectPHP' => [
                                [
                                    'String'           => 'one',
                                    'Number'           => 1.1,
                                    'Integer'          => 1,
                                    'EnumString'       => 'one',
                                    'EnumNumber'       => 1.1,
                                    'EnumInteger'      => 1,
                                    'ArrayString'      => ['one'],
                                    'ArrayNumber'      => [1.1],
                                    'ArrayInteger'     => [1],
                                    'ArrayEnumString'  => ['one'],
                                    'ArrayEnumNumber'  => [1.1],
                                    'ArrayEnumInteger' => [1],
                                ],
                            ],
                        ],

                        'Documented Object' => [
                            'Object' => [
                                'String'           => 'one',
                                'Number'           => 1.1,
                                'Integer'          => 1,
                                'EnumString'       => 'one',
                                'EnumNumber'       => 1.1,
                                'EnumInteger'      => 1,
                                'ArrayString'      => ['one'],
                                'ArrayNumber'      => [1.1],
                                'ArrayInteger'     => [1],
                                'ArrayEnumString'  => ['one'],
                                'ArrayEnumNumber'  => [1.1],
                                'ArrayEnumInteger' => [1],
                            ],
                        ],

                        'Documented Object with undocumented properties' => [
                            'Object' => [
                                'EnumString'               => 'two',
                                'EnumNumber'               => 2.2,
                                'EnumInteger'              => 2,
                                'ArrayEnumString'          => ['two'],
                                'ArrayEnumNumber'          => [2.2],
                                'ArrayEnumInteger'         => [2],
                                'UndocumentedString'       => 'three',
                                'UndocumentedNumber'       => 3.3,
                                'UndocumentedInteger'      => 3,
                                'UndocumentedArrayString'  => ['three'],
                                'UndocumentedArrayNumber'  => [3.3],
                                'UndocumentedArrayInteger' => [3],
                                'UndocumentedObject'       => [
                                    'String'  => 'three',
                                    'Number'  => 3.3,
                                    'Integer' => 3,
                                ],
                            ],
                        ],
                        */
        ];

        foreach ($params as $queryParams) {
            $this->recordHttpCall('get', 'http://server/resource', 200, $queryParams);
        }

        $spec = $this->coverage->process($this->container->getSpecFile('query.yaml'), $this->recorder);

        $path = $spec->path('/resource');
        $get  = $path->operation('get');

        $this->print($spec);

        $params = [
            'String',
            'Number',
            'Integer',
        ];
        foreach ($params as $paramName) {
            $param = $this->assertQueryParameter($get, $paramName, true, true);

            $this->assertDocumented($param);
            $this->assertExecuted($param);
            $this->assertDocumented($param->getType());
            $this->assertExecuted($param->getType());
        }

        $params = [
            'EnumString'  => [['one'], ['two'], ['three']],
            'EnumNumber'  => [[1.1], [2.2], [3.3]],
            'EnumInteger' => [[1], [2], [3]],
        ];
        foreach ($params as $paramName => $paramParams) {
            [$documentedExcetuted, $notExecuted, $undocumented] = $paramParams;

            $param = $get->findQueryParameter($paramName);

            $this->assertDocumented($param);
            $this->assertExecuted($param);

            /** @var TypeEnum $enum */
            $enum = $param->getType();
            $this->assertDocumented($enum);
            $this->assertExecuted($enum);

            $this->assertSame($documentedExcetuted, $enum->getDocumentedExecutedEnum());
            $this->assertSame($notExecuted, $enum->getNotExecutedEnum());
            $this->assertSame($undocumented, $enum->getUndocumentedEnum());
        }

        $params = [
            'ArrayString',
            'ArrayNumber',
            'ArrayInteger',
        ];
        foreach ($params as $paramName) {
            $param = $get->findQueryParameter($paramName);

            $this->assertDocumented($param);
            $this->assertExecuted($param);
        }

        $params = [
            'ArrayEnumString'  => [['one'], ['two'], ['three']],
            'ArrayEnumNumber'  => [[1.1], [2.2], [3.3]],
            'ArrayEnumInteger' => [[1], [2], [3]],
        ];
        foreach ($params as $paramName => $paramParams) {
            [$documentedExcetuted, $notExecuted, $undocumented] = $paramParams;

            $param = $get->findQueryParameter($paramName);

            $this->assertDocumented($param);
            $this->assertExecuted($param);

            /** @var TypeArray $array */
            $array = $param->getType();
            $this->assertDocumented($array);

            // TODO: fix
            // $this->assertExecuted($array);

            /** @var TypeEnum $enum */
            $enum = $array->getType();
            $this->assertDocumented($enum);
            $this->assertExecuted($enum);

            $this->assertSame($documentedExcetuted, $enum->getDocumentedExecutedEnum());
            $this->assertSame($notExecuted, $enum->getNotExecutedEnum());
            $this->assertSame($undocumented, $enum->getUndocumentedEnum());
        }

        // /** @var Parameter $undocumentedObjectParameter */
        // $undocumentedObjectParameter = $get->findQueryParameter('UndocumentedObject');
        // $this->assertNotDocumented($undocumentedObjectParameter);
        // /** @var TypeObject $undocumentedObjectParameterType */
        // $undocumentedObjectParameterType = $undocumentedObjectParameter->getType();
        // foreach ($undocumentedObjectParameterType->getProperties() as $property) {
        //     $this->assertNotDocumented($property);
        // }
    }
}
