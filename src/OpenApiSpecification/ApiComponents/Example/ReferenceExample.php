<?php declare(strict_types=1);

namespace App\OpenApiSpecification\ApiComponents\Example;

use App\OpenApiSpecification\ApiComponents\Example;
use App\OpenApiSpecification\ApiComponents\Reference;

final class ReferenceExample extends Example
{
    private Reference $reference;
    private DetailedExample $example;

    private function __construct(
        Reference $reference,
        DetailedExample $example,
        ?ExampleName $name = null
    ) {
        $this->reference = $reference;
        $this->example = $example;
        $this->name = $name;
    }

    public static function generate(string $objectName, DetailedExample $example): self
    {
        return new self(Reference::generateExampleReference($objectName), $example);
    }

    public function setName(string $name): self
    {
        return new self($this->reference, $this->example, ExampleName::fromString($name));
    }

    public function toDetailedExample(): DetailedExample
    {
        return $this->example;
    }

    public function getLiteralValue(): array
    {
        return $this->example->getLiteralValue();
    }

    public function toOpenApiSpecification(): array
    {
        return $this->reference->toOpenApiSpecification();
    }
}