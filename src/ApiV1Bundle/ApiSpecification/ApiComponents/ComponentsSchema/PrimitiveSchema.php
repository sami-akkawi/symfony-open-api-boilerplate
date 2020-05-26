<?php declare(strict=1);
// Created by sami-akkawi on 16.05.20

namespace App\ApiV1Bundle\ApiSpecification\ApiComponents\ComponentsSchema;

use App\ApiV1Bundle\ApiSpecification\ApiComponents\Schema;

abstract class PrimitiveSchema extends Schema
{
    public abstract static function generate(?string $name = null);

    public abstract function setDescription(string $description);

    public abstract function setExample(string $example);

    public abstract function toOpenApiSpecification(): array;
}