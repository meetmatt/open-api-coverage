<?php

declare(strict_types=1);

namespace MeetMatt\OpenApiSpecCoverage;

use MeetMatt\OpenApiSpecCoverage\Specification\Content;
use MeetMatt\OpenApiSpecCoverage\Specification\Operation;
use MeetMatt\OpenApiSpecCoverage\Specification\Parameter;
use MeetMatt\OpenApiSpecCoverage\Specification\Path;
use MeetMatt\OpenApiSpecCoverage\Specification\Property;
use MeetMatt\OpenApiSpecCoverage\Specification\RequestBody;
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

    public function process(string $specFile, TestRecorder $testRecorder): void
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
                $request    = $log->getRequest();
                $response   = $log->getResponse();
                $uriPath    = $request->getUri()->getPath();
                $httpMethod = $request->getMethod();

                // TODO: match path and operation directly by operationId if it's set during the test with coversOperationId($id)
                // -> introduce new log entity with type CoversOperationId

                $path = $specification->findPath($uriPath);
                if ($path === null) {
                    // Undocumented path
                    $path = new Path($uriPath);
                    $path->setIsDocumented(false);
                    $specification->addPath($path);
                }

                $operation = $path->findOperation($httpMethod);
                if ($operation === null) {
                    // Undocumented operation
                    $operation = new Operation($httpMethod);
                    $operation->setIsDocumented(false);
                    $path->addOperation($operation);
                }

                // Mark path and operation as executed
                $path->setIsExecuted(true);
                $operation->setIsExecuted(true);

                $passedQueryParameters = $request->getQueryParams();
                foreach ($passedQueryParameters as $passedQueryParameterName => $passedQueryParameterValue) {
                    // Find passed parameter in specification
                    $passedQueryParameterType    = $this->convertToType($passedQueryParameterValue);
                    $specificationQueryParameter = $operation->findQueryParameter($passedQueryParameterName, $passedQueryParameterType);

                    $passedQueryParameterExistsInSpecification = $specificationQueryParameter !== null;

                    if ($passedQueryParameterExistsInSpecification) {
                        // Documented parameter
                        $specificationQueryParameter->setIsExecuted(true);
                        $doTypesMatch = $this->compareTypes($passedQueryParameterType, $specificationQueryParameter->getType());
                        if (!$doTypesMatch) {
                            // types don't match, need to add a new undocumented parameter
                            $passedQueryParameterExistsInSpecification = false;
                        }
                    }

                    if (!$passedQueryParameterExistsInSpecification) {
                        // Undocumented parameter
                        $undocumentedQueryParameterType = $passedQueryParameterType;
                        $undocumentedQueryParameter     = new Parameter($passedQueryParameterName, $undocumentedQueryParameterType);
                        $operation->addQueryParameter($undocumentedQueryParameter);

                        $undocumentedQueryParameter->setIsDocumented(false);
                        $undocumentedQueryParameter->setIsExecuted(true);
                        $undocumentedQueryParameterType->setIsExecuted(true);
                        $undocumentedQueryParameterType->setIsDocumented(false);
                    }
                }

                // Request path parameters
                $pathParameters = $operation->getPathParameters();
                if (!empty($pathParameters)) {
                    // Since the path was found in the spec, means that all path parameters matched something, so we mark all of them as executed
                    foreach ($pathParameters as $pathParameter) {
                        $pathParameter->setIsExecuted(true);
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
    }

    private function compareTypes(TypeAbstract $passedType, TypeAbstract $specType): bool
    {
        if ($passedType instanceof TypeArray && $specType instanceof TypeArray) {
            if (get_class($passedType->getType()) === get_class($specType->getType())) {
                // array element types match
                $specType->setIsExecuted(true);

                return true;
            }

            // array element types don't match -> add a new undocumented element to the spec with a different array type
            return false;
        }

        if ($passedType instanceof TypeObject && $specType instanceof TypeObject) {
            $specProperties            = $specType->getProperties();
            $unmatchedPassedProperties = $passedType->getProperties();
            $passedProperties          = $unmatchedPassedProperties;
            foreach ($specProperties as $specPropertyName => $specProperty) {
                foreach ($passedProperties as $passedPropertyName => $passedProperty) {
                    if ($passedPropertyName === $specPropertyName) {
                        // -> spec property was executed
                        $specProperty->setIsExecuted(true);

                        // properties have the same name, but we need to check that types match
                        $doPropertyTypesMatch = $this->compareTypes($passedProperty->getType(), $specProperty->getType());

                        if (!$doPropertyTypesMatch) {
                            // -> passed property matched, so we don't need to create that undocumented property
                            unset($unmatchedPassedProperties[$specPropertyName]);
                        }
                    }
                }
            }

            // add all unmatched passed properties as undocumented
            foreach ($unmatchedPassedProperties as $undocumentedProperty) {
                $undocumentedProperty->setIsExecuted(true);
                $undocumentedProperty->setIsDocumented(false);
                $specType->addProperty($undocumentedProperty);
            }

            // Always return true, because we don't want to create a completely new object on the parent, only additional undocumented properties
            return true;
        }

        if ($specType instanceof TypeEnum && $passedType instanceof TypeScalar) {
            // the element is declared as enum, but we can actually only pass scalars in the request, so passedType will never be TypeEnum
            if ($passedType->getType() === $specType->getScalarType()->getType()) {
                $specType->setEnumValueAsExecuted($passedType->getValue());
            } else {
                // passed type doesn't match the enum type -> create a new element in the parent with a different enum scalar type
                return false;
            }
        } elseif ($passedType instanceof TypeScalar && $specType instanceof TypeScalar) {
            return $passedType->getType() === $specType->getType();
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
                $properties = [];
                foreach ($value as $v) {
                    // recursively convert values to types
                    $properties[] = $this->convertToType($v);
                }

                return new TypeObject($properties);
            }

            // ordinary list
            // determine the type of array elements by looking at the first element

            // TODO: edge case: can be an array of mixed types, e.g. strings, floats, arrays ...
            // it will match to the anyOf type in the specification; this is why TypeArray has multiple TypeAbstracts internally

            $type = $this->convertToType(current($value));

            return new TypeArray($type);
        }

        if ($this->isIntegerish($value)) {
            return new TypeScalar('integer', (int)$value);
        }

        if ($this->isFloatish($value)) {
            return new TypeScalar('number', (float)$value);
        }

        return new TypeScalar('string', (string)$value);
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

    private function isIntegerish($value): bool
    {
        if (is_int($value)) {
            return true;
        }

        /** @noinspection TypeUnsafeComparisonInspection */
        return (int)$value == $value;
    }

    private function isFloatish($value): bool
    {
        if (is_float($value)) {
            return true;
        }

        // Works properly only in PHP >= 8.0
        // return (float)$value == $value;

        return preg_match('/(0|[1-9]+)?\.\d*/', $value) === 1;
    }
}
