<?php declare(strict_types=1);

namespace App\OpenApiSpecification\ApiComponents\ComponentsExample;

use App\OpenApiSpecification\ApiComponents\ComponentsExample;
use App\OpenApiSpecification\ApiComponents\ComponentsExample\Example\ExampleName;
use App\OpenApiSpecification\ApiComponents\Reference;

final class ReferenceExample extends ComponentsExample
{
    private Reference $reference;
    private Example $example;

    private function __construct(
        Reference $reference,
        Example $example,
        ?ExampleName $name = null
    ) {
        $this->reference = $reference;
        $this->example = $example;
        $this->name = $name;
    }

    public static function generate(string $objectName, Example $example): self
    {
        return new self(Reference::generateExampleReference($objectName), $example);
    }

    public function setName(string $name): self
    {
        return new self($this->reference, $this->example, ExampleName::fromString($name));
    }

    public function toExample(): Example
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