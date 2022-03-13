<?php

declare(strict_types=1);

namespace MeetMatt\OpenApiSpecCoverage;

use MeetMatt\OpenApiSpecCoverage\Specification\Parameter;
use MeetMatt\OpenApiSpecCoverage\Specification\Property;
use MeetMatt\OpenApiSpecCoverage\Specification\Specification;
use MeetMatt\OpenApiSpecCoverage\Specification\SpecificationException;
use MeetMatt\OpenApiSpecCoverage\Specification\SpecificationFactoryInterface;
use MeetMatt\OpenApiSpecCoverage\Specification\TypeAbstract;
use MeetMatt\OpenApiSpecCoverage\Specification\TypeArray;
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
                        $undocumentedQueryParameterType = $passedParamType;
                        $undocumentedQueryParameter     = new Parameter(
                            $passedParamName,
                            $undocumentedQueryParameterType
                        );
                        $operation->addQueryParameter($undocumentedQueryParameter);

                        $undocumentedQueryParameter->undocumented();
                        $undocumentedQueryParameter->executed();
                        $undocumentedQueryParameterType->undocumented();
                        $undocumentedQueryParameterType->executed();
                    }
                }

                // Request path parameters
                $pathParameters = $operation->getPathParameters();
                if (!empty($pathParameters)) {
                    // Since the path was found in the spec, means that all path parameters matched something, so we mark all of them as executed
                    foreach ($pathParameters as $pathParameter) {
                        $pathParameter->executed();
                    }

                    // TODO: enum path parameters need to be tracked against passed values
                    // In order to accomplish it we need to figure out which path parameters were passed in the $uriPath
                    // a good idea would be to record it during the $specification->findPath($uriPath) method, save it temporarily in the object
                    // as an associative array, and use it to check that spec parameters defined as enums were executed for each possible value
                    // basically we need to do for each detected passed parameter:
                    // $this->compareTypes($this->convertToType($detectedPassedPathParameter), $pathParameter->getType())
                    // -> in the end we might find some undocumented enum values or those which were never executed
                    // undocumented/executed will be figured out during the report generation at the very end
                }

                // TODO: request bodies
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
                $undocumentedProperty->getType()->executed();
                $undocumentedProperty->getType()->undocumented();
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
                        &&
                        in_array($passedScalarType, ['float', 'integer'], true)
                    )
                ) {
                    $specType->executed();

                    return true;
                };
            } elseif ($specType instanceof TypeEnum) {
                if ($this->compareTypes($passedType, $specType->getScalarType())) {
                    $specType->setEnumValueAsExecuted($passedType->getValue());

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
            if ($this->isObject($value)) {
                // associative array is an object
                $object = (new TypeObject())->executed();
                foreach ($value as $name => $spec) {
                    $object->addProperty($name, $this->convertToType($spec))->executed();
                }

                return $object;
            }

            // ordinary list
            // determine the type of array elements by looking at the first element

            // TODO: edge case: can be an array of mixed types, e.g. strings, floats, arrays ...
            // it will match to the anyOf type in the specification; this is why TypeArray has multiple TypeAbstracts internally

            $type = $this->convertToType(current($value))->executed();

            return (new TypeArray($type))->executed();
        }

        if ($this->isIntegerish($value)) {
            return (new TypeScalar('integer', (int)$value))->executed();
        }

        if ($this->isFloatish($value)) {
            return (new TypeScalar('number', (float)$value))->executed();
        }

        return (new TypeScalar('string', (string)$value))->executed();
    }

    private function isObject(array $array): bool
    {
        // Can be replaced with !array_is_list($array) in PHP 8.1

        foreach ($array as $key => $value) {
            if (!$this->isIntegerish($key)) {
                // if there's at least one non-integer key, then it's an assoc object
                return true;
            }
        }

        return false;
    }

    private
    function isIntegerish(
        $value
    ): bool {
        if (is_int($value)) {
            return true;
        }

        /** @noinspection TypeUnsafeComparisonInspection */
        return (int)$value == $value;
    }

    private
    function isFloatish(
        $value
    ): bool {
        if (is_float($value)) {
            return true;
        }

        // Works properly only in PHP >= 8.0
        // return (float)$value == $value;

        return preg_match('/(0|[1-9]+)?\.\d*/', $value) === 1;
    }
}
