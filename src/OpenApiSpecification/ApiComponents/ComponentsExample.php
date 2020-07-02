<?php declare(strict_types=1);

namespace App\OpenApiSpecification\ApiComponents;

use App\OpenApiSpecification\ApiComponents\ComponentsExample\Example;
use App\OpenApiSpecification\ApiComponents\ComponentsExample\Example\ExampleName;

abstract class ComponentsExample
{
    protected ?ExampleName $name;

    abstract public function toExample(): Example;

    abstract public function setName(string $name);

    public function isValidForSchema(ComponentsSchema $schema): array
    {
        return $schema->isValueValid($this->toExample()->getLiteralValue());
    }

    public function getName(): ?ExampleName
    {
        return $this->name;
    }

    public function hasName(): bool
    {
        return (bool)$this->name;
    }

    abstract public function getLiteralValue();

    abstract public function toOpenApiSpecification(): array;
}