<?php

declare(strict_types=1);

namespace MeetMatt\OpenApiSpecCoverage\OpenApi;

use cebe\openapi\spec\Schema;
use MeetMatt\OpenApiSpecCoverage\Specification\SpecificationException;
use MeetMatt\OpenApiSpecCoverage\Specification\TypeAbstract;
use MeetMatt\OpenApiSpecCoverage\Specification\TypeArray;
use MeetMatt\OpenApiSpecCoverage\Specification\TypeEnum;
use MeetMatt\OpenApiSpecCoverage\Specification\TypeObject;
use MeetMatt\OpenApiSpecCoverage\Specification\TypeScalar;

class OpenApiSchemaParser
{
    public function parse(Schema $schema): TypeAbstract
    {
        if (isset($schema->allOf) && is_iterable($schema->allOf)) {
            $type = new TypeObject();
            foreach ($schema->allOf as $scheme) {
                $parsed = $this->parse($scheme);
                if ($parsed instanceof TypeObject) {
                    foreach ($parsed->getProperties() as $property) {
                        $type->addProperty($property);
                    }
                } else {
                    // TODO: allOf can be only objects, right?
                    throw SpecificationException::unsupportedSpecificationDefinition('allOff can be only object.');
                }
            }
        } elseif (isset($schema->oneOf) && is_iterable($schema->oneOf)) {
            // TODO: implement mapping of oneOf
            throw SpecificationException::unsupportedSpecificationDefinition('oneOff is not yet supported.');
        } elseif (isset($schema->anyOf) && is_iterable($schema->anyOf)) {
            // TODO: implement mapping of anyOf
            throw SpecificationException::unsupportedSpecificationDefinition('anyOff is not yet supported.');
        } else {
            switch ($schema->type) {
                case 'array':
                    $type = new TypeArray($this->parse($schema->items));
                    break;

                case 'object':
                    $type = new TypeObject();
                    foreach ($schema->properties as $name => $property) {
                        $prop = $type->addProperty($name, $this->parse($property));
                        $prop->documented();
                    }
                    break;

                default:
                    $type = new TypeScalar($schema->type);
                    if (isset($schema->enum) && is_iterable($schema->enum)) {
                        $enum = $schema->enum;
                        $type = new TypeEnum($type->documented(), $enum);
                    }
            }
        }

        $type->documented();

        return $type;
    }
}
