<?php

declare(strict_types=1);

namespace MeetMatt\OpenApiSpecCoverage;

use MeetMatt\OpenApiSpecCoverage\Specification\Parameter;
use MeetMatt\OpenApiSpecCoverage\Specification\Property;
use MeetMatt\OpenApiSpecCoverage\Specification\RequestBody;
use MeetMatt\OpenApiSpecCoverage\Specification\Specification;
use MeetMatt\OpenApiSpecCoverage\Specification\SpecificationException;
use MeetMatt\OpenApiSpecCoverage\Specification\SpecificationFactoryInterface;
use MeetMatt\OpenApiSpecCoverage\Specification\TypeAbstract;
use MeetMatt\OpenApiSpecCoverage\Specification\TypeArray;
use MeetMatt\OpenApiSpecCoverage\Specification\Typed;
use MeetMatt\OpenApiSpecCoverage\Specification\TypeEnum;
use MeetMatt\OpenApiSpecCoverage\Specification\TypeObject;
use MeetMatt\OpenApiSpecCoverage\Specification\TypeScalar;
use MeetMatt\OpenApiSpecCoverage\TestRecorder\HttpCall;
use MeetMatt\OpenApiSpecCoverage\TestRecorder\ResponseContentAssertion;
use MeetMatt\OpenApiSpecCoverage\TestRecorder\ResponseContentTypeAssertion;
use MeetMatt\OpenApiSpecCoverage\TestRecorder\ResponseStatusCodeAssertion;
use MeetMatt\OpenApiSpecCoverage\TestRecorder\TestRecorder;

class Coverage
{
    private SpecificationFactoryInterface $factory;

    public function __construct(SpecificationFactoryInterface $factory)
    {
        $this->factory = $factory;
    }

    /**
     * @throws SpecificationException
     */
    public function process(string $specFile, TestRecorder $testRecorder): Specification
    {
        // Parse specification
        $specification = $this->factory->fromFile($specFile);

        // These variable should remain defined between iterations for each HTTP call context
        // because we need to know which operation response parameters we should mark as asserted
        $operation = null;
        $response  = null;

        foreach ($testRecorder->getLogs() as $log) {
            // On each HTTP call we find the path and operation in the specification
            if ($log instanceof HttpCall) {
                $request  = $log->getRequest();
                $response = $log->getResponse();

                $uriPath = $request->getUri()->getPath();
                $path    = $specification->path($uriPath);
                if ($path === null) {
                    $path = $specification->addPath($uriPath);
                }
                $path->executed();

                $httpMethod = strtolower($request->getMethod());
                $operation  = $path->operation($httpMethod);
                if ($operation === null) {
                    $operation = $path->addOperation($httpMethod);
                    foreach ($operation->getPathParameters() as $pathParameter) {
                        $pathParameter->getType()->executed();
                    }
                }
                $operation->executed();

                $passedQueryParams = $request->getQueryParams();
                foreach ($passedQueryParams as $passedParamName => $passedParamValue) {
                    $specParam = $operation->findQueryParameter($passedParamName);

                    $passedParamExistsInSpec = $specParam !== null;

                    $passedParamType = $this->convertToType($passedParamValue);
                    if ($passedParamExistsInSpec) {
                        $specParam->executed();
                        $doTypesMatch = $this->diffTypes($passedParamType, $specParam->getType());
                        if (!$doTypesMatch) {
                            // types don't match -> new undocumented parameter
                            $passedParamExistsInSpec = false;
                        }
                    }

                    if (!$passedParamExistsInSpec) {
                        $undocumentedQueryParameter = new Parameter($passedParamName, $passedParamType);
                        $operation->addQueryParameter($undocumentedQueryParameter);
                        $undocumentedQueryParameter->executed();
                        $passedParamType->executed();
                    }
                }

                $matchedPathParameters = $path->getMatchedPathParameters();
                foreach ($operation->getPathParameters() as $pathParameter) {
                    if (!array_key_exists($pathParameter->getName(), $matchedPathParameters)) {
                        continue;
                    }

                    $pathParameter->executed();

                    $matchedPathParameterValue  = $matchedPathParameters[$pathParameter->getName()];
                    $operationPathParameterType = $pathParameter->getType();
                    $pathParameter->getType()->executed();
                    if ($operationPathParameterType instanceof TypeEnum) {
                        $operationPathParameterType->setEnumValueAsExecuted($matchedPathParameterValue);
                    }
                }

                $contentType = $request->getHeader('Content-Type');
                $parsedBody  = $request->getParsedBody();
                if (!empty($contentType) && $parsedBody !== null) {
                    $passedContent = $this->convertToType($parsedBody);
                    $requestBody   = $operation->findRequestBody($contentType[0]);

                    $passedContentExists = $requestBody !== null;
                    if ($passedContentExists) {
                        $requestBody->executed();
                        if (!$this->diffTypes($passedContent, $requestBody->getType())) {
                            $passedContentExists = false;
                        }
                    }

                    if (!$passedContentExists) {
                        $requestBody = new RequestBody($contentType[0], $passedContent);
                        $requestBody->executed();
                        $operation->addRequestBody($requestBody);
                    }
                }
                // TODO: calculate coverage of response contents
            }

            if ($operation === null || $response === null) {
                // assertion was called without a prior API call
                continue;
            }

            if ($log instanceof ResponseStatusCodeAssertion) {
                // TODO: calculate coverage of response status code by assertion
            }

            if ($log instanceof ResponseContentTypeAssertion) {
                // TODO: calculate coverage of response content type by assertion
            }

            if ($log instanceof ResponseContentAssertion) {
                // TODO: calculate coverage of response content by assertion
            }
        }

        return $specification;
    }

    private function diffTypes(TypeAbstract $passedType, TypeAbstract $specType): bool
    {
        if ($passedType instanceof TypeArray && $specType instanceof TypeArray) {
            return $this->diffTypes($passedType->getType(), $specType->getType());
        }

        if ($passedType instanceof TypeObject && $specType instanceof TypeObject) {
            $passedProperties = $passedType->getProperties();

            $undocumentedProperties = [];
            foreach ($passedProperties as $prop) {
                $undocumentedProperties[$prop->getName()] = clone $prop;
            }

            foreach ($specType->getProperties() as $specProperty) {
                foreach ($passedProperties as $passedProperty) {
                    if ($passedProperty->getName() === $specProperty->getName()) {
                        $specProperty->executed();

                        if ($this->diffTypes($passedProperty->getType(), $specProperty->getType())) {
                            $specProperty->getType()->executed();
                            unset($undocumentedProperties[$specProperty->getName()]);
                        }
                    }
                }
            }

            foreach ($undocumentedProperties as $undocumentedProperty) {
                $undocumentedProperty->executed();
                $this->markAsExecuted($undocumentedProperty->getType());
                $specType->addProperty($undocumentedProperty);
            }

            return true;
        }

        if ($passedType instanceof TypeScalar) {
            if ($specType instanceof TypeScalar) {
                $passedScalarType = $passedType->getType();
                $specScalarType   = $specType->getType();
                if (
                    $passedScalarType === $specScalarType
                    || (
                        in_array($specScalarType, ['float', 'number'], true)
                        && in_array($passedScalarType, ['float', 'integer'], true)
                    )
                ) {
                    $this->markAsExecuted($specType);

                    return true;
                }
            } elseif ($specType instanceof TypeEnum) {
                if ($this->diffTypes($passedType, $specType->getType())) {
                    if ($passedType->getValue() !== null) {
                        $specType->setEnumValueAsExecuted($passedType->getValue());
                    }

                    $this->markAsExecuted($specType);

                    return true;
                }
            }
        }

        return false;
    }

    /**
     * @param array|int|float|string $value
     *
     * @return TypeAbstract
     */
    private function convertToType($value): TypeAbstract
    {
        if (empty($value)) {
            $returnedType = new TypeScalar('string');
        } elseif (is_array($value)) {
            if ($this->arrayIsList($value)) {
                // TODO: multi-type arrays (anyOf, allOf)
                $firstValue = current($value);
                $type       = $this->convertToType($firstValue);

                $returnedType = new TypeArray($type);
            } else {
                $returnedType = new TypeObject();
                foreach ($value as $name => $spec) {
                    $returnedType->addProperty($name, $this->convertToType($spec))->executed();
                }
            }
        } elseif ($this->isIntegerish($value)) {
            $returnedType = new TypeScalar('integer', (int)$value);
        } elseif ($this->isFloatish($value)) {
            $returnedType = new TypeScalar('number', (float)$value);
        } else {
            $returnedType = new TypeScalar('string', (string)$value);
        }

        $returnedType->executed();

        return $returnedType;
    }

    private function arrayIsList(array $array): bool
    {
        // TODO: Replaced with array_is_list($array) in PHP 8.1

        $keys = array_keys($array);
        foreach ($keys as $i => $key) {
            if ((int)$i !== (int)$key) {
                return false;
            }
        }

        return true;
    }

    private function isIntegerish($value): bool
    {
        if (is_int($value)) {
            return true;
        }

        return preg_match('/^\d+$/', (string)$value) === 1;
    }

    private function isFloatish($value): bool
    {
        if (is_float($value)) {
            return true;
        }

        // TODO: Replace with return (float)$value == $value; in PHP 8.0

        return preg_match('/(0|[1-9]+)?\.\d*/', (string)$value) === 1;
    }

    private function markAsExecuted(TypeAbstract $type): void
    {
        $type->executed();
        if ($type instanceof Typed) {
            $this->markAsExecuted($type->getType());
        }
    }
}
