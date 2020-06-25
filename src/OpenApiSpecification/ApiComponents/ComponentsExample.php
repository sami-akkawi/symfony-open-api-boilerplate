<?php declare(strict_types=1);

namespace App\OpenApiSpecification\ApiComponents;

use App\OpenApiSpecification\ApiComponents\ComponentsExample\Example;
use App\OpenApiSpecification\ApiComponents\ComponentsExample\Example\ExampleName;

abstract class ComponentsExample
{
    protected ?ExampleName $name;

    public abstract function toExample(): Example;

    public abstract function setName(string $name);

    public function isValidForSchema(Schema $schema): array
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

    public abstract function getLiteralValue();

    public abstract function toOpenApiSpecification(): array;
}