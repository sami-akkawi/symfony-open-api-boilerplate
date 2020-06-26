<?php declare(strict_types=1);

namespace App\OpenApiSpecification\ApiComponents\ComponentsSchema;

use App\OpenApiSpecification\ApiComponents\ComponentsSchema\Schema\SchemaType;

abstract class PrimitiveSchema extends Schema
{
    protected SchemaType $type;

    public function getType(): SchemaType
    {
        return $this->type;
    }

    public abstract static function generate();

    public abstract function setDescription(string $description);
}