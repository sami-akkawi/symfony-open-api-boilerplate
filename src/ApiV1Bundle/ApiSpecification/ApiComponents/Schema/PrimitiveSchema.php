<?php declare(strict=1);

namespace App\ApiV1Bundle\ApiSpecification\ApiComponents\Schema;

use App\ApiV1Bundle\ApiSpecification\ApiComponents\Schema;

abstract class PrimitiveSchema extends Schema
{
    public abstract static function generate(?string $name = null);

    public abstract function setDescription(string $description);

    public abstract function setExample(string $example);

    public abstract function toOpenApiSpecification(): array;
}