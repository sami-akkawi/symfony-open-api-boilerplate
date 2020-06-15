<?php declare(strict=1);

namespace App\ApiV1Bundle\ApiSpecification\ApiComponents\Schema;

use App\ApiV1Bundle\ApiSpecification\ApiComponents\Schema;

abstract class DetailedSchema extends Schema
{
    public function toDetailedSchema(): DetailedSchema
    {
        return $this;
    }

    protected function getWrongTypeMessage(string $correctType, $value): string
    {
        return "Should be $correctType, " . gettype($value) . ' supplied.';
    }
}