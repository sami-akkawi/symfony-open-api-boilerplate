<?php declare(strict_types=1);

namespace App\OpenApiSpecification\ApiComponents;

use App\OpenApiSpecification\ApiComponents\SecurityScheme\SecurityScheme\SchemeName;
use App\OpenApiSpecification\ApiException\SpecificationException;

final class SecuritySchemes
{
    /** @var SecurityScheme[] */
    private array $schemes;

    private function __construct(array $schemes)
    {
        $this->schemes = $schemes;
    }

    public static function generate(): self
    {
        return new self([]);
    }

    private function hasScheme(SchemeName $name): bool
    {
        foreach ($this->schemes as $scheme) {
            if ($scheme->getSchemeName()->isIdenticalTo($name)) {
                return true;
            }
        }
        return false;
    }

    public function addScheme(SecurityScheme $scheme): self
    {
        if ($this->hasScheme($scheme->getSchemeName())) {
            throw SpecificationException::generateDuplicateDefinitionException($scheme->getSchemeName()->toString());
        }
        return new self(array_merge($this->schemes, [$scheme]));
    }

    public function toOpenApiSpecification(): array
    {
        $specifications = [];

        foreach ($this->schemes as $scheme) {
            $specifications[$scheme->getSchemeName()->toString()] = $scheme->toOpenApiSpecification();
        }

        return $specifications;
    }

    public function isDefined(): bool
    {
        return (bool)count($this->schemes);
    }
}