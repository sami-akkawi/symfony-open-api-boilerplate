<?php declare(strict_types=1);

namespace App\OpenApiSpecification\ApiComponents\ComponentsHeader;

use App\OpenApiSpecification\ApiComponents\ComponentsExample;
use App\OpenApiSpecification\ApiComponents\ComponentsExamples;
use App\OpenApiSpecification\ApiComponents\ComponentsHeader\Header\HeaderIsRequired;
use App\OpenApiSpecification\ApiComponents\ComponentsHeader\Header\HeaderIsDeprecated;
use App\OpenApiSpecification\ApiComponents\ComponentsHeader\Header\HeaderDescription;
use App\OpenApiSpecification\ApiComponents\ComponentsHeader\Header\HeaderKey;
use App\OpenApiSpecification\ApiComponents\ComponentsSchema;

abstract class SchemaHeader extends DetailedHeader
{
    protected ComponentsSchema $schema;

    protected function __construct(
        HeaderIsRequired $isRequired,
        HeaderIsDeprecated $isDeprecated,
        ComponentsSchema $schema,
        ?HeaderDescription $description = null,
        ?HeaderKey $key = null,
        ?ComponentsExample $example = null,
        ?ComponentsExamples $examples = null
    ) {
        parent::__construct($isRequired, $isDeprecated, $description, $key, $example, $examples);
        $this->schema = $schema;
    }

    abstract public function makeNullable();

    public function toOpenApiSpecification(): array
    {
        $specification = [
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

        if ($this->example) {
            $specification['example'] = $this->example->toOpenApiSpecification();
        }

        if ($this->examples) {
            $specification['examples'] = $this->examples->toOpenApiSpecification();
        }

        return $specification;
    }
}