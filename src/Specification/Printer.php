<?php

declare(strict_types=1);

namespace MeetMatt\OpenApiSpecCoverage\Specification;

use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Helper\TableCell;
use Symfony\Component\Console\Output\ConsoleOutput;

class Printer
{
    public function print(Specification $specification): void
    {
        $data = [];

        foreach ($specification->getPaths() as $path) {
            $uriPath = $path->getUriPath();
            if (!isset($data[$uriPath])) {
                $data[$uriPath] = [];
            }
            foreach ($path->getOperations() as $operation) {
                $httpMethod = $operation->getHttpMethod();
                if (!isset($data[$uriPath][$httpMethod])) {
                    $data[$uriPath][$httpMethod] = [];
                }

                $types = [];
                foreach ($operation->getPathParameters() as $parameter) {
                    $types += self::flattenTypeTree('path.' . $parameter->getName(), $parameter->getType());
                }
                foreach ($operation->getQueryParameters() as $parameter) {
                    $types += self::flattenTypeTree('query.' . $parameter->getName(), $parameter->getType());
                }
                foreach ($operation->getRequestBodies() as $requestBody) {
                    $types += self::flattenTypeTree('request.' . $requestBody->getContentType(), $requestBody->getType());
                }

                // TODO: response contents
                // foreach ($operation->getResponses() as $response) {
                //     foreach ($response->getContents() as $responseBody) {
                //         foreach ($responseBody->getProperties() as $property) {
                //             $types += self::flattenTypeTree('response.' . $property->getName(), $property->getType());
                //         }
                //     }
                // }

                foreach ($types as $key => $value) {
                    if (!isset($value[0])) {
                        $value = [$value];
                    }
                    foreach ($value as $val) {
                        $data[$uriPath][$httpMethod][] = [
                            $key,
                            $val['v'],
                            $val['d'],
                            $val['x'],
                        ];
                    }
                }
            }
        }

        $output = new ConsoleOutput();
        $table  = new Table($output);
        $table->setStyle('box');
        $table->setHeaders(['Path', 'HTTP Method', 'Element', 'Type', 'Documented', 'Executed']);
        foreach ($data as $path => $operations) {
            foreach ($operations as $operation => $params) {
                $table->addRow(
                    [
                        new TableCell($path, ['rowspan' => count($params) + 1]),
                        new TableCell($operation, ['rowspan' => count($params) + 1]),
                    ]
                );

                foreach ($params as $param) {
                    $table->addRow($param);
                }
            }
        }

        echo "\n";
        ob_flush();
        $table->render();
    }

    private static function flattenTypeTree(string $name, TypeAbstract $type): array
    {
        $flat = [];

        if ($type instanceof TypeScalar) {
            $flat[$name] = [
                'v' => '<' . $type->getType() . '>',
                'd' => $type->isDocumented() ? '+' : '-',
                'x' => $type->isExecuted() ? '+' : '-',
            ];
        } elseif ($type instanceof TypeEnum) {
            $flat[$name] = [];

            $documentedCoveredEnums = $type->getDocumentedExecutedEnum();
            if (!empty($documentedCoveredEnums)) {
                $flat[$name][] = [
                    'v' => '<' . $type->getType()->getType() . '>' . json_encode($documentedCoveredEnums),
                    'd' => '+',
                    'x' => '+',
                ];
            }

            $uncoveredEnums = $type->getNonExecutedEnum();
            if (!empty($uncoveredEnums)) {
                $flat[$name][] = [
                    'v' => '<' . $type->getType()->getType() . '>' . json_encode($uncoveredEnums),
                    'd' => '+',
                    'x' => '-',
                ];
            }

            $undocumentedEnums = $type->getUndocumentedEnum();
            if (!empty($undocumentedEnums)) {
                $flat[$name][] = [
                    'v' => '<' . $type->getType()->getType() . '>' . json_encode($undocumentedEnums),
                    'd' => '-',
                    'x' => '+',
                ];
            }
        } elseif ($type instanceof TypeArray) {
            foreach (self::flattenTypeTree($name . '[]', $type->getType()) as $key => $value) {
                if (isset($value['v'])) {
                    $value = [$value];
                }
                $flat[$key] = [];
                foreach ($value as $val) {
                    $flat[$key][] = [
                        'v' => $val['v'],
                        'd' => $val['d'],
                        'x' => $val['x'],
                    ];
                }
            }
        } elseif ($type instanceof TypeObject) {
            foreach ($type->getProperties() as $property) {
                $propertyPrefix = $name . '.' . $property->getName();
                $flattenedProperty = self::flattenTypeTree($propertyPrefix, $property->getType());
                foreach ($flattenedProperty as $key => $value) {
                    if (isset($value['v'])) {
                        $value = [$value];
                    }
                    $flat[$key] = [];
                    foreach ($value as $val) {
                        $flat[$key][] = [
                            'v' => $val['v'],
                            'd' => $val['d'],
                            'x' => $val['x'],
                        ];
                    }
                }
            }
        }

        return $flat;
    }
}
