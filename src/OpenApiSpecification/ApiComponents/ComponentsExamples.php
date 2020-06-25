<?php declare(strict_types=1);

namespace App\OpenApiSpecification\ApiComponents;

use App\OpenApiSpecification\ApiComponents\ComponentsExample\Example\ExampleName;
use App\OpenApiSpecification\ApiException\SpecificationException;

final class ComponentsExamples
{
    /** @var ComponentsExample[] */
    private array $examples;

    private function __construct(array $examples)
    {
        $this->examples = $examples;
    }

    public static function generate(): self
    {
        return new self([]);
    }

    private function hasExample(string $name): bool
    {
        foreach ($this->examples as $example) {
            if ($example->getName()->isIdenticalTo(ExampleName::fromString($name))) {
                return true;
            }
        }

        return false;
    }

    public function addExample(ComponentsExample $example, string $name): self
    {
        if ($this->hasExample($name)) {
            throw SpecificationException::generateDuplicateExamples();
        }

        return new self(array_merge($this->examples, [$example->setName($name)]));
    }

    public function isDefined(): bool
    {
        return (bool)count($this->examples);
    }

    public function toOpenApiSpecification(): array
    {
        if (!$this->isDefined()) {
            throw SpecificationException::generateResponsesMustBeDefined();
        }
        $parameters = [];
        foreach ($this->examples as $example) {
            $parameters[$example->getName()->toString()] = $example->toOpenApiSpecification();
        }
        return $parameters;
    }
}