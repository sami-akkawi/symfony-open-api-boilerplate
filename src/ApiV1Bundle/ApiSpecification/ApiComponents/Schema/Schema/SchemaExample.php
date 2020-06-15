<?php declare(strict=1);

namespace App\ApiV1Bundle\ApiSpecification\ApiComponents\Schema\Schema;

final class SchemaExample
{
    /** @var mixed */
    private $example;

    private function __construct($example)
    {
        $this->example = $example;
    }

    public static function fromAny($example): self
    {
        return new self($example);
    }

    public function toAny()
    {
        return $this->example;
    }
}