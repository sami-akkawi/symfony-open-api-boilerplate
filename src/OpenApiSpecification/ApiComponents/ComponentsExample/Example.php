<?php declare(strict_types=1);

namespace App\OpenApiSpecification\ApiComponents\ComponentsExample;

use App\OpenApiSpecification\ApiComponents\ComponentsExample;
use App\OpenApiSpecification\ApiComponents\ComponentsExample\Example\ExampleDescription;
use App\OpenApiSpecification\ApiComponents\ComponentsExample\Example\ExampleName;
use App\OpenApiSpecification\ApiComponents\ComponentsExample\Example\ExampleSummary;
use App\OpenApiSpecification\ApiComponents\ComponentsExample\Example\ExampleValue;

final class Example extends ComponentsExample
{
    private ExampleValue $value;
    private ?ExampleSummary $summary;
    private ?ExampleDescription $description;

    private function __construct(
        $example,
        ?ExampleName $name = null,
        ?ExampleSummary $summary = null,
        ?ExampleDescription $description = null
    ) {
        $this->value = $example;
        $this->name = $name;
        $this->summary = $summary;
        $this->description = $description;
    }

    public function setName(string $name): self
    {
        return new self($this->value, ExampleName::fromString($name), $this->summary, $this->description);
    }

    public function setSummary(string $summary): self
    {
        return new self($this->value, $this->name, ExampleSummary::fromString($summary), $this->description);
    }

    public function setDescription(string $description): self
    {
        return new self($this->value, $this->name, $this->summary, ExampleDescription::fromString($description));
    }

    public function toExample(): Example
    {
        return $this;
    }

    public static function generate($example): self
    {
        return new self(ExampleValue::generate($example));
    }

    public function getLiteralValue()
    {
        return $this->value->toMixed();
    }

    public function toOpenApiSpecification(): array
    {
        $specifications = [
            'value' => $this->value->toMixed()
        ];
        if ($this->summary) {
            $specifications['summary'] = $this->summary->toString();
        }
        if ($this->description) {
            $specifications['description'] = $this->description->toString();
        }
        return $specifications;
    }
}