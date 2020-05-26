<?php declare(strict=1);

namespace App\ApiV1Bundle\ApiSpecification;

use App\ApiV1Bundle\ApiSpecification\ApiComponents\Schema;
use App\ApiV1Bundle\ApiSpecification\ApiComponents\Schemas;
use App\ApiV1Bundle\ApiSpecification\ApiComponents\SecurityScheme;
use App\ApiV1Bundle\ApiSpecification\ApiComponents\SecuritySchemes;

final class ApiComponents
{
    private Schemas $schemas;
    // todo: responses
    // todo: parameters
    // todo: examples
    // todo: requestBodies
    // todo: headers
    private SecuritySchemes $securitySchemes;
    // todo: links

    private function __construct(Schemas $schemas, SecuritySchemes $securitySchemes)
    {
        $this->schemas = $schemas;
        $this->securitySchemes = $securitySchemes;
    }

    public static function generate(): self
    {
        return new self(Schemas::generate(), SecuritySchemes::generate());
    }

    public function addSecurityScheme(SecurityScheme $scheme): self
    {
        return new self(
            $this->schemas,
            $this->securitySchemes->addScheme($scheme)
        );
    }

    public function addSchema(Schema $schema): self
    {
        return new self(
            $this->schemas->addSchema($schema),
            $this->securitySchemes
        );
    }

    public function toOpenApiSpecification(): array
    {
        $specifications = [];
        if ($this->schemas->hasValues()) {
            $specifications['schemas'] =  $this->schemas->toOpenApiSpecification(true);
        }
        if ($this->securitySchemes->isDefined()) {
            $specifications['securitySchemes'] =  $this->securitySchemes->toOpenApiSpecification();
        }
        return $specifications;
    }

    public function isDefined(): bool
    {
        return (
            $this->schemas->hasValues() ||
            $this->securitySchemes->isDefined()
        );
    }
}