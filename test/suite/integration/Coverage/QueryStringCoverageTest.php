<?php

declare(strict_types=1);

namespace MeetMatt\OpenApiSpecCoverage\Test\Suite\Integration\Coverage;

use MeetMatt\OpenApiSpecCoverage\Specification\Parameter;
use MeetMatt\OpenApiSpecCoverage\Specification\TypeArray;
use MeetMatt\OpenApiSpecCoverage\Specification\TypeObject;
use MeetMatt\OpenApiSpecCoverage\Test\Support\CoverageTestCase;

class QueryStringCoverageTest extends CoverageTestCase
{
    public function testQueryStringCoverage(): void
    {
        $params = [
            [
                'ArrayObjectPHP'   => [
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
                    [
                        'String'           => 'two',
                        'Number'           => 2.2,
                        'Integer'          => 2,
                        'EnumString'       => 'two',
                        'EnumNumber'       => 2.2,
                        'EnumInteger'      => 2,
                        'ArrayString'      => ['two'],
                        'ArrayNumber'      => [2.2],
                        'ArrayInteger'     => [2],
                        'ArrayEnumString'  => ['two'],
                        'ArrayEnumNumber'  => [2.2],
                        'ArrayEnumInteger' => [2],
                    ],
                ],
                'ArrayObject'      => [
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
                    [
                        'String'           => 'two',
                        'Number'           => 2.2,
                        'Integer'          => 2,
                        'EnumString'       => 'two',
                        'EnumNumber'       => 2.2,
                        'EnumInteger'      => 2,
                        'ArrayString'      => ['two'],
                        'ArrayNumber'      => [2.2],
                        'ArrayInteger'     => [2],
                        'ArrayEnumString'  => ['two'],
                        'ArrayEnumNumber'  => [2.2],
                        'ArrayEnumInteger' => [2],
                    ],
                ],
                'Object'           => [
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
            [
                'ArrayObjectPHP'   => [
                    [
                        'String'           => 'two',
                        'Number'           => 2.2,
                        'Integer'          => 2,
                        'EnumString'       => 'two',
                        'EnumNumber'       => 2.2,
                        'EnumInteger'      => 2,
                        'ArrayString'      => ['two'],
                        'ArrayNumber'      => [2.2],
                        'ArrayInteger'     => [2],
                        'ArrayEnumString'  => ['two'],
                        'ArrayEnumNumber'  => [2.2],
                        'ArrayEnumInteger' => [2],
                    ],
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
                'ArrayObject'      => [
                    [
                        'String'           => 'two',
                        'Number'           => 2.2,
                        'Integer'          => 2,
                        'EnumString'       => 'two',
                        'EnumNumber'       => 2.2,
                        'EnumInteger'      => 2,
                        'ArrayString'      => ['two'],
                        'ArrayNumber'      => [2.2],
                        'ArrayInteger'     => [2],
                        'ArrayEnumString'  => ['two'],
                        'ArrayEnumNumber'  => [2.2],
                        'ArrayEnumInteger' => [2],
                    ],
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
                'Object'           => [
                    'String'           => 'two',
                    'Number'           => 2.2,
                    'Integer'          => 2,
                    'EnumString'       => 'two',
                    'EnumNumber'       => 2.2,
                    'EnumInteger'      => 2,
                    'ArrayString'      => ['two'],
                    'ArrayNumber'      => [2.2],
                    'ArrayInteger'     => [2],
                    'ArrayEnumString'  => ['two'],
                    'ArrayEnumNumber'  => [2.2],
                    'ArrayEnumInteger' => [2],
                ],
                'String'           => 'two',
                'Number'           => 2.2,
                'Integer'          => 2,
                'EnumString'       => 'two',
                'EnumNumber'       => 2.2,
                'EnumInteger'      => 2,
                'ArrayString'      => ['two'],
                'ArrayNumber'      => [2.2],
                'ArrayInteger'     => [2],
                'ArrayEnumString'  => ['two'],
                'ArrayEnumNumber'  => [2.2],
                'ArrayEnumInteger' => [2],
            ],
            [
                'ArrayObjectPHP'           => [
                    [
                        'String'                   => 'three',
                        'Number'                   => 3.3,
                        'Integer'                  => 3,
                        'EnumString'               => 'three',
                        'EnumNumber'               => 3.3,
                        'EnumInteger'              => 3,
                        'ArrayString'              => ['three'],
                        'ArrayNumber'              => [3.3],
                        'ArrayInteger'             => [3],
                        'ArrayEnumString'          => ['three'],
                        'ArrayEnumNumber'          => [3.3],
                        'ArrayEnumInteger'         => [3],
                        'UndocumentedArrayObject'  => [
                            [
                                'String'       => 'three',
                                'Number'       => 3.3,
                                'Integer'      => 3,
                                'ArrayString'  => ['three'],
                                'ArrayNumber'  => [3.3],
                                'ArrayInteger' => [3],
                            ],
                        ],
                        'UndocumentedObject'       => [
                            'String'       => 'three',
                            'Number'       => 3.3,
                            'Integer'      => 3,
                            'ArrayString'  => ['three'],
                            'ArrayNumber'  => [3.3],
                            'ArrayInteger' => [3],
                        ],
                        'UndocumentedString'       => 'four',
                        'UndocumentedNumber'       => 4.4,
                        'UndocumentedInteger'      => 4,
                        'UndocumentedArrayString'  => ['four', 'five'],
                        'UndocumentedArrayNumber'  => [4.4, 5.5],
                        'UndocumentedArrayInteger' => [4, 5],
                    ],
                    [
                        'String'                   => 'three',
                        'Number'                   => 3.3,
                        'Integer'                  => 3,
                        'EnumString'               => 'three',
                        'EnumNumber'               => 3.3,
                        'EnumInteger'              => 3,
                        'ArrayString'              => ['three'],
                        'ArrayNumber'              => [3.3],
                        'ArrayInteger'             => [3],
                        'ArrayEnumString'          => ['three'],
                        'ArrayEnumNumber'          => [3.3],
                        'ArrayEnumInteger'         => [3],
                        'UndocumentedArrayObject'  => [
                            [
                                'String'       => 'three',
                                'Number'       => 3.3,
                                'Integer'      => 3,
                                'ArrayString'  => ['three'],
                                'ArrayNumber'  => [3.3],
                                'ArrayInteger' => [3],
                            ],
                        ],
                        'UndocumentedObject'       => [
                            'String'       => 'three',
                            'Number'       => 3.3,
                            'Integer'      => 3,
                            'ArrayString'  => ['three'],
                            'ArrayNumber'  => [3.3],
                            'ArrayInteger' => [3],
                        ],
                        'UndocumentedString'       => 'four',
                        'UndocumentedNumber'       => 4.4,
                        'UndocumentedInteger'      => 4,
                        'UndocumentedArrayString'  => ['four', 'five'],
                        'UndocumentedArrayNumber'  => [4.4, 5.5],
                        'UndocumentedArrayInteger' => [4, 5],
                    ],
                ],
                'ArrayObject'              => [
                    [
                        'String'                   => 'three',
                        'Number'                   => 3.3,
                        'Integer'                  => 3,
                        'EnumString'               => 'three',
                        'EnumNumber'               => 3.3,
                        'EnumInteger'              => 3,
                        'ArrayEnumString'          => ['three'],
                        'ArrayEnumNumber'          => [3.3],
                        'ArrayEnumInteger'         => [3],
                        'UndocumentedArrayObject'  => [
                            [
                                'String'       => 'three',
                                'Number'       => 3.3,
                                'Integer'      => 3,
                                'ArrayString'  => ['three'],
                                'ArrayNumber'  => [3.3],
                                'ArrayInteger' => [3],
                            ],
                        ],
                        'UndocumentedObject'       => [
                            'String'       => 'three',
                            'Number'       => 3.3,
                            'Integer'      => 3,
                            'ArrayString'  => ['three'],
                            'ArrayNumber'  => [3.3],
                            'ArrayInteger' => [3],
                        ],
                        'UndocumentedString'       => 'four',
                        'UndocumentedNumber'       => 4.4,
                        'UndocumentedInteger'      => 4,
                        'UndocumentedArrayString'  => ['four', 'five'],
                        'UndocumentedArrayNumber'  => [4.4, 5.5],
                        'UndocumentedArrayInteger' => [4, 5],
                    ],
                    [
                        'String'                   => 'three',
                        'Number'                   => 3.3,
                        'Integer'                  => 3,
                        'EnumString'               => 'three',
                        'EnumNumber'               => 3.3,
                        'EnumInteger'              => 3,
                        'ArrayString'              => ['three'],
                        'ArrayNumber'              => [3.3],
                        'ArrayInteger'             => [3],
                        'ArrayEnumString'          => ['three'],
                        'ArrayEnumNumber'          => [3.3],
                        'ArrayEnumInteger'         => [3],
                        'UndocumentedArrayObject'  => [
                            [
                                'String'       => 'three',
                                'Number'       => 3.3,
                                'Integer'      => 3,
                                'ArrayString'  => ['three'],
                                'ArrayNumber'  => [3.3],
                                'ArrayInteger' => [3],
                            ],
                        ],
                        'UndocumentedObject'       => [
                            'String'       => 'three',
                            'Number'       => 3.3,
                            'Integer'      => 3,
                            'ArrayString'  => ['three'],
                            'ArrayNumber'  => [3.3],
                            'ArrayInteger' => [3],
                        ],
                        'UndocumentedString'       => 'four',
                        'UndocumentedNumber'       => 4.4,
                        'UndocumentedInteger'      => 4,
                        'UndocumentedArrayString'  => ['four', 'five'],
                        'UndocumentedArrayNumber'  => [4.4, 5.5],
                        'UndocumentedArrayInteger' => [4, 5],
                    ],
                ],
                'Object'                   => [
                    'String'                   => 'three',
                    'Number'                   => 3.3,
                    'Integer'                  => 3,
                    'EnumString'               => 'three',
                    'EnumNumber'               => 3.3,
                    'EnumInteger'              => 3,
                    'ArrayString'              => ['three'],
                    'ArrayNumber'              => [3.3],
                    'ArrayInteger'             => [3],
                    'ArrayEnumString'          => ['three'],
                    'ArrayEnumNumber'          => [3.3],
                    'ArrayEnumInteger'         => [3],
                    'UndocumentedArrayObject'  => [
                        [
                            'String'       => 'three',
                            'Number'       => 3.3,
                            'Integer'      => 3,
                            'ArrayString'  => ['three'],
                            'ArrayNumber'  => [3.3],
                            'ArrayInteger' => [3],
                        ],
                    ],
                    'UndocumentedObject'       => [
                        'String'       => 'three',
                        'Number'       => 3.3,
                        'Integer'      => 3,
                        'ArrayString'  => ['three'],
                        'ArrayNumber'  => [3.3],
                        'ArrayInteger' => [3],
                    ],
                    'UndocumentedString'       => 'four',
                    'UndocumentedNumber'       => 4.4,
                    'UndocumentedInteger'      => 4,
                    'UndocumentedArrayString'  => ['four', 'five'],
                    'UndocumentedArrayNumber'  => [4.4, 5.5],
                    'UndocumentedArrayInteger' => [4, 5],
                ],
                'String'                   => 'three',
                'Number'                   => 3.3,
                'Integer'                  => 3,
                'EnumString'               => 'three',
                'EnumNumber'               => 3.3,
                'EnumInteger'              => 3,
                'ArrayString'              => ['three'],
                'ArrayNumber'              => [3.3],
                'ArrayInteger'             => [3],
                'ArrayEnumString'          => ['three'],
                'ArrayEnumNumber'          => [3.3],
                'ArrayEnumInteger'         => [3],
                'UndocumentedArrayObject'  => [
                    [
                        'String'       => 'three',
                        'Number'       => 3.3,
                        'Integer'      => 3,
                        'ArrayString'  => ['three'],
                        'ArrayNumber'  => [3.3],
                        'ArrayInteger' => [3],
                    ],
                ],
                'UndocumentedObject'       => [
                    'String'       => 'three',
                    'Number'       => 3.3,
                    'Integer'      => 3,
                    'ArrayString'  => ['three'],
                    'ArrayNumber'  => [3.3],
                    'ArrayInteger' => [3],
                ],
                'UndocumentedString'       => 'four',
                'UndocumentedNumber'       => 4.4,
                'UndocumentedInteger'      => 4,
                'UndocumentedArrayString'  => ['four', 'five'],
                'UndocumentedArrayNumber'  => [4.4, 5.5],
                'UndocumentedArrayInteger' => [4, 5],
            ],
        ];

        foreach ($params as $queryParams) {
            $this->recordHttpCall('get', 'http://server/resource', 200, $queryParams);
        }

        $spec = $this->coverage->process($this->container->getSpecFile('query.yaml'), $this->recorder);

        $path = $spec->path('/resource');
        $this->assertNotNull($path);

        $get = $path->operation('get');
        $this->assertNotNull($get);

        $this->printer->print($spec);

        $arrayObjectPhp = $get->findQueryParameters('ArrayObjectPHP');
        $this->assertCount(1, $arrayObjectPhp);
        /** @var Parameter $arrayObjectPhp */
        $arrayObjectPhp = current($arrayObjectPhp);
        $this->assertDocumented($arrayObjectPhp);
        $this->assertDocumented($arrayObjectPhp->getType());
        $this->assertInstanceOf(TypeArray::class, $arrayObjectPhp->getType());

        // TODO: fix finding of the parameters like this: ArrayObjectPHP[0][UndocumentedArrayObject] or ArrayObjectPHP[][UndocumentedArrayObject]

        /** @var Parameter $undocumentedObjectParameter */
        $undocumentedObjectParameter = current($get->findQueryParameters('UndocumentedObject'));
        $this->assertNotDocumented($undocumentedObjectParameter);
        /** @var TypeObject $undocumentedObjectParameterType */
        $undocumentedObjectParameterType = $undocumentedObjectParameter->getType();
        foreach ($undocumentedObjectParameterType->getProperties() as $property) {
            $this->assertNotDocumented($property);
        }

        // TODO: add more asserts for all cases
    }
}
