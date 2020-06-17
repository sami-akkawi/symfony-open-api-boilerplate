<?php declare(strict=1);

namespace App\ApiV1Bundle\ApiSpecification\ApiComponents;

use App\ApiV1Bundle\ApiSpecification\ApiComponents\Example\ExampleName;
use App\ApiV1Bundle\ApiSpecification\ApiException\SpecificationException;

final class Examples
{
    /** @var Example[] */
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

    public function addExample(Example $example, string $name): self
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