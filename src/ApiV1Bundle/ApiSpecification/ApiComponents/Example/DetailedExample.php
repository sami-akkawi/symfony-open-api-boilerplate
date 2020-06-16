<?php declare(strict=1);

namespace App\ApiV1Bundle\ApiSpecification\ApiComponents\Example;

use App\ApiV1Bundle\ApiSpecification\ApiComponents\Example;

final class DetailedExample extends Example
{
    /** @var mixed */
    private $example;

    private function __construct($example, ?ExampleName $name = null)
    {
        $this->example = $example;
        $this->name = $name;
    }

    public function setName(string $name): self
    {
        return new self($this->example, ExampleName::fromString($name));
    }

    public function toDetailedExample(): DetailedExample
    {
        return $this;
    }

    public static function generate($example): self
    {
        return new self($example);
    }

    public function toMixed()
    {
        return $this->example;
    }
}