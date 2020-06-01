<?php declare(strict=1);

namespace App\ApiV1Bundle\ApiSpecification\ApiComponents\Parameter;

use App\ApiV1Bundle\ApiSpecification\ApiComponents\Schema;

abstract class SchemaParameter extends DetailedParameter
{
    protected Schema $schema;

    protected function __construct(
        ParameterName $name,
        ParameterLocation $location,
        ParameterIsRequired $isRequired,
        ParameterIsDeprecated $isDeprecated,
        Schema $schema,
        ?ParameterDescription $description = null,
        ?ParameterDocName $docName = null
    ) {
        parent::__construct($name, $location, $isRequired, $isDeprecated, $description, $docName);
        $this->schema = $schema;
    }

    public abstract function makeNullable();

    public function toOpenApiSpecification(): array
    {
        $specification = [
            'name' => $this->name->toString(),
            'in' => $this->location->toString(),
            'schema' => $this->schema->toOpenApiSpecification()
        ];

        if ($this->isRequired->toBool()) {
            $specification['required'] = true;
        }

        if ($this->isDeprecated->toBool()) {
            $specification['deprecated'] = true;
        }

        if ($this->description) {
            $specification['description'] = $this->description->toString();
        }

        return $specification;
    }
}