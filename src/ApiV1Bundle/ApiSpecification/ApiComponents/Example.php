<?php declare(strict=1);

namespace App\ApiV1Bundle\ApiSpecification\ApiComponents;

use App\ApiV1Bundle\ApiSpecification\ApiComponents\Example\DetailedExample;
use App\ApiV1Bundle\ApiSpecification\ApiComponents\Example\ExampleName;

abstract class Example
{
    protected ?ExampleName $name;

    public abstract function toDetailedExample(): DetailedExample;

    public abstract function setName(string $name);

    public function isValidForSchema(Schema $schema): array
    {
        return $schema->isValueValid($this->toDetailedExample()->toMixed());
    }

    public function getName(): ?ExampleName
    {
        return $this->name;
    }

    public abstract function toMixed();
}