<?php

declare(strict_types=1);

namespace MeetMatt\OpenApiSpecCoverage\Specification;

use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Output\ConsoleOutput;

class Printer
{
    public function print(Specification $specification): void
    {
        $data = [];

        foreach ($specification->getPaths() as $path) {
            foreach ($path->getOperations() as $operation) {
                $types = [];
                foreach ($operation->getPathParameters() as $parameter) {
                    $types += self::flattenTypeTree($parameter->getName(), $parameter->getType());
                }
                foreach ($operation->getQueryParameters() as $parameter) {
                    $types += self::flattenTypeTree($parameter->getName(), $parameter->getType());
                }
                foreach ($operation->getResponses() as $response) {
                    foreach ($response->getContents() as $responseBody) {
                        foreach ($responseBody->getProperties() as $property) {
                            $types += self::flattenTypeTree($property->getName(), $property->getType());
                        }
                    }
                }
                foreach ($operation->getRequestBodies() as $requestBody) {
                    foreach ($requestBody->getProperties() as $property) {
                        $types += self::flattenTypeTree($property->getName(), $property->getType());
                    }
                }

                foreach ($types as $key => $value) {
                    if (!isset($value[0])) {
                        $value = [$value];
                    }
                    foreach ($value as $val) {
                        $data[] = [
                            $path->getUriPath(),
                            $operation->getHttpMethod(),
                            $key,
                            $val['v'],
                            $val['d'],
                            $val['x']
                        ];
                    }
                }
            }
        }

        $output = new ConsoleOutput();
        $table  = new Table($output);
        $table->setHeaders(['Path', 'HTTP Method', 'Parameter', 'Type', 'Documented', 'Executed']);
        $table->addRows($data);
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
                    'v' => '<' . $type->getScalarType()->getType() . '>' . json_encode($documentedCoveredEnums),
                    'd' => '+',
                    'x' => '+',
                ];
            }

            $uncoveredEnums = $type->getNonExecutedEnum();
            if (!empty($uncoveredEnums)) {
                $flat[$name][] = [
                    'v' => '<' . $type->getScalarType()->getType() . '>' . json_encode($uncoveredEnums),
                    'd' => '+',
                    'x' => '-',
                ];
            }

            $undocumentedEnums = $type->getUndocumentedEnum();
            if (!empty($undocumentedEnums)) {
                $flat[$name][] = [
                    'v' => '<' . $type->getScalarType()->getType() . '>' . json_encode($undocumentedEnums),
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
                $propertyPrefix    = $name . '.' . $property->getName();
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
