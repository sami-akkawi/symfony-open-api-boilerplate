<?php declare(strict_types=1);

namespace App\OpenApiSpecification\ApiComponents\Schema;

use App\Message\Message;
use App\OpenApiSpecification\ApiComponents\Schema;

abstract class DetailedSchema extends Schema
{
    public function toDetailedSchema(): DetailedSchema
    {
        return $this;
    }

    protected function getWrongTypeMessage(string $correctType, $value): Message
    {
        return Message::generateError(
            'incorrect_type_supplied',
            "Should be $correctType, " . gettype($value) . ' supplied.',
            [
                '%correctType%' => $correctType,
                '%suppliedType' => gettype($value)
            ]
        );
    }
}