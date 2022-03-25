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
                    $path->undocumented();
                }
                $path->executed();

                $httpMethod = strtolower($request->getMethod());
                $operation  = $path->operation($httpMethod);
                if ($operation === null) {
                    $operation = $path->addOperation($httpMethod);
                    $operation->undocumented();
                    foreach ($operation->getPathParameters() as $pathParameter) {
                        $this->markAsUndocumented($pathParameter->getType());
                        $this->markAsExecuted($pathParameter->getType());
                    }
                }
                $operation->executed();

                $passedQueryParams = $request->getQueryParams();
                foreach ($passedQueryParams as $passedParamName => $passedParamValue) {
                    // Find passed parameter in specification
                    $passedParamType = $this->convertToType($passedParamValue);
                    $specParam       = $operation->findQueryParameter($passedParamName, $passedParamType);

                    $passedParamExistsInSpec = $specParam !== null;

                    if ($passedParamExistsInSpec) {
                        // Documented parameter
                        $specParam->executed();

                        $specParamType = $specParam->getType();
                        $doTypesMatch  = $this->compareTypes($passedParamType, $specParamType);
                        if (!$doTypesMatch) {
                            // types don't match, need to add a new undocumented parameter
                            $passedParamExistsInSpec = false;
                        }
                    }

                    if (!$passedParamExistsInSpec) {
                        // Undocumented parameter
                        $undocumentedQueryParameter = new Parameter($passedParamName, $passedParamType);
                        $operation->addQueryParameter($undocumentedQueryParameter);

                        $undocumentedQueryParameter->undocumented();
                        $undocumentedQueryParameter->executed();
                        $this->markAsExecuted($passedParamType);
                        $this->markAsUndocumented($passedParamType);
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
                    $passedContent = $this->convertToType($parsedBody)->undocumented();
                    $requestBody   = $operation->findRequestBody($contentType[0]);

                    $passedContentExists = $requestBody !== null;
                    if ($passedContentExists) {
                        $requestBody->executed();
                        if (!$this->compareTypes($passedContent, $requestBody->getType())) {
                            $passedContentExists = false;
                        }
                    }

                    if (!$passedContentExists) {
                        $requestBody = new RequestBody($contentType[0], $passedContent);
                        $requestBody->undocumented();
                        $requestBody->executed();
                        $operation->addRequestBody($requestBody);
                    }
                }
                // TODO: response contents
            }

            if ($operation === null || $response === null) {
                // assertion was called without a prior API call
                continue;
            }

            if ($log instanceof ResponseStatusCodeAssertion) {
                // TODO: response status code assertion
            }

            if ($log instanceof ResponseContentTypeAssertion) {
                // TODO: response content type assertion
            }

            if ($log instanceof ResponseContentAssertion) {
                // TODO: response content assertion
            }
        }

        return $specification;
    }

    private function compareTypes(TypeAbstract $passedType, TypeAbstract $specType): bool
    {
        if ($passedType instanceof TypeArray && $specType instanceof TypeArray) {
            if ($this->compareTypes($passedType->getType(), $specType->getType())) {
                $specType->executed();

                return true;
            }

            // array element types don't match -> add a new undocumented element to the spec with a different array type
            return false;
        }

        if ($passedType instanceof TypeObject && $specType instanceof TypeObject) {
            $specProperties         = $specType->getProperties();
            $passedProperties       = $passedType->getProperties();
            $undocumentedProperties = array_combine(
                array_map(static fn(Property $property) => $property->getName(), $passedProperties),
                $passedProperties
            );

            foreach ($specProperties as $specProperty) {
                foreach ($passedProperties as $passedProperty) {
                    $passedPropertyName = $passedProperty->getName();
                    $specPropertyName   = $specProperty->getName();
                    if ($passedPropertyName === $specPropertyName) {
                        $passedPropertyType = $passedProperty->getType();
                        $specPropertyType   = $specProperty->getType();

                        $specProperty->executed();
                        $doPropertyTypesMatch = $this->compareTypes($passedPropertyType, $specPropertyType);
                        if ($doPropertyTypesMatch) {
                            $specPropertyType->executed();
                            unset($undocumentedProperties[$specPropertyName]);
                        }
                    }
                }
            }

            // add all unmatched passed properties as undocumented
            /** @var Property[] $undocumentedProperties */
            foreach ($undocumentedProperties as $undocumentedProperty) {
                $undocumentedProperty->executed();
                $undocumentedProperty->undocumented();
                $this->markAsExecuted($undocumentedProperty->getType());
                $this->markAsUndocumented($undocumentedProperty->getType());
                $specType->addProperty($undocumentedProperty);
            }

            // Always return true, because we don't want to create a completely new object on the parent, only additional undocumented properties
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
                if ($this->compareTypes($passedType, $specType->getType())) {
                    if ($passedType->getValue() !== null) {
                        $specType->setEnumValueAsExecuted($passedType->getValue());
                    }

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
        if (is_array($value)) {
            if ($this->arrayIsList($value)) {
                // ordinary list
                // determine the type of array elements by looking at the first element

                // TODO: edge case: can be an array of mixed types, e.g. strings, floats, arrays ...
                // it will match to the anyOf type in the specification; this is why TypeArray has multiple TypeAbstracts internally

                $type = !empty($value) ? $this->convertToType(current($value))->executed() : new TypeScalar('string');

                return (new TypeArray($type))->executed();
            }

            // if array is not a list, then it's an object
            // associative array is an object
            $object = (new TypeObject())->executed();
            foreach ($value as $name => $spec) {
                $object->addProperty($name, $this->convertToType($spec))->executed();
            }

            return $object;
        }

        if ($this->isIntegerish($value)) {
            return (new TypeScalar('integer', (int)$value))->executed();
        }

        if ($this->isFloatish($value)) {
            return (new TypeScalar('number', (float)$value))->executed();
        }

        return (new TypeScalar('string', (string)$value))->executed();
    }

    private function arrayIsList(array $array): bool
    {
        // Can be replaced with array_is_list($array) in PHP 8.1

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

        // Works properly only in PHP >= 8.0
        // return (float)$value == $value;

        return preg_match('/(0|[1-9]+)?\.\d*/', (string)$value) === 1;
    }

    private function markAsUndocumented(TypeAbstract $type): void
    {
        $type->undocumented();
        if ($type instanceof Typed) {
            $this->markAsUndocumented($type->getType());
        }
    }

    private function markAsExecuted(TypeAbstract $type): void
    {
        $type->executed();
        if ($type instanceof Typed) {
            $this->markAsExecuted($type->getType());
        }
    }
}
