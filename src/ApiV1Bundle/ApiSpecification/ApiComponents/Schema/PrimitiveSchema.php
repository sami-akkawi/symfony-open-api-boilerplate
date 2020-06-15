<?php declare(strict=1);

namespace App\ApiV1Bundle\ApiSpecification\ApiComponents\Schema;

abstract class PrimitiveSchema extends DetailedSchema
{
    public abstract static function generate();

    public abstract function setDescription(string $description);
}