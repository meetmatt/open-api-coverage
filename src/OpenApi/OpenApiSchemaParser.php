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
        // TODO: oneOf
        // TODO: anyOf

        if (isset($schema->allOf) && is_iterable($schema->allOf)) {
            $properties = [];
            foreach ($schema->allOf as $scheme) {
                $object = $this->parse($scheme);
                if ($object instanceof TypeObject) {
                    foreach ($object->getProperties() as $property) {
                        $properties[] = $property;
                    }
                }
                // elseif ...
                // TODO: allOf can be only objects, right?
            }

            return new TypeObject($properties);
        }

        switch ($schema->type) {
            case 'array':
                $type = new TypeArray($this->parse($schema->items));
                break;

            case 'object':
                $properties = [];
                foreach ($schema->properties as $name => $property) {
                    $properties[] = new Property($name, $this->parse($property));
                }
                $type = new TypeObject($properties);
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
