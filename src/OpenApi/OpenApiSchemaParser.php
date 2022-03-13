<?php

declare(strict_types=1);

namespace MeetMatt\OpenApiSpecCoverage\OpenApi;

use cebe\openapi\spec\Schema;
use MeetMatt\OpenApiSpecCoverage\Specification\Property;
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
            $object = new TypeObject();
            foreach ($schema->allOf as $scheme) {
                $parsed = $this->parse($scheme);
                if ($parsed instanceof TypeObject) {
                    foreach ($parsed->getProperties() as $property) {
                        $object->addProperty($property);
                    }
                }
                // elseif ...
                // TODO: allOf can be only objects, right?
            }

            return $object;
        }

        // TODO: oneOf
        // TODO: anyOf

        switch ($schema->type) {
            case 'array':
                $type = new TypeArray($this->parse($schema->items));
                break;

            case 'object':
                $type = new TypeObject();
                foreach ($schema->properties as $name => $property) {
                    $type->addProperty($name, $this->parse($property));
                }

                break;

            default:
                $type = new TypeScalar($schema->type);
                if (isset($schema->enum) && is_iterable($schema->enum)) {
                    $enum = $schema->enum;
                    $type = new TypeEnum($type, $enum);
                }
        }

        return $type;
    }
}
