<?php declare(strict_types=1);

namespace App\OpenApiSpecification\ApiComponents\ComponentsSchema;

use App\Message\Message;
use App\OpenApiSpecification\ApiComponents\ComponentsSchema;

abstract class Schema extends ComponentsSchema
{
    public function toSchema(): Schema
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