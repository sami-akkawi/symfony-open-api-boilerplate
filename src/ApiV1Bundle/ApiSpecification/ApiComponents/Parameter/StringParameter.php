<?php declare(strict=1);

namespace App\ApiV1Bundle\ApiSpecification\ApiComponents\Parameter;

use App\ApiV1Bundle\ApiSpecification\ApiComponents\Schema\StringSchema;
use App\ApiV1Bundle\ApiSpecification\ApiComponents\Schema;

final class StringParameter extends SchemaParameter
{
    /** @var StringSchema $schema  */
    protected Schema $schema;

    private function __construct(
        ParameterName $name,
        ParameterLocation $location,
        ParameterIsRequired $isRequired,
        ParameterIsDeprecated $isDeprecated,
        StringSchema $schema,
        ?ParameterDescription $description = null
    ) {
        parent::__construct($name, $location, $isRequired, $isDeprecated, $schema, $description);
    }

    private static function generate(string $name, ParameterLocation $location): self
    {
        return new self(
            ParameterName::fromString($name),
            $location,
            $location->isInPath() ? ParameterIsRequired::generateTrue() : ParameterIsRequired::generateFalse(),
            ParameterIsDeprecated::generateFalse(),
            StringSchema::generate()
        );
    }

    public static function generateInCookie(string $name): self
    {
        return self::generate($name, ParameterLocation::generateCookie());
    }

    public static function generateInQuery(string $name): self
    {
        return self::generate($name, ParameterLocation::generateQuery());
    }

    public static function generateInHeader(string $name): self
    {
        return self::generate($name, ParameterLocation::generateHeader());
    }

    public static function generateInPath(string $name): self
    {
        return self::generate($name, ParameterLocation::generatePath());
    }

    public function setFormat(string $format): self
    {
        return new self(
            $this->name,
            $this->location,
            $this->isRequired,
            $this->isDeprecated,
            $this->schema->setFormat($format),
            $this->description
        );
    }

    public function require(): self
    {
        return new self(
            $this->name,
            $this->location,
            ParameterIsRequired::generateTrue(),
            $this->isDeprecated,
            $this->schema,
            $this->description
        );
    }

    public function deprecate(): self
    {
        return new self(
            $this->name,
            $this->location,
            $this->isRequired,
            ParameterIsDeprecated::generateTrue(),
            $this->schema,
            $this->description
        );
    }

    public function setDescription(string $description): self
    {
        return new self(
            $this->name,
            $this->location,
            $this->isRequired,
            $this->isDeprecated,
            $this->schema,
            ParameterDescription::fromString($description)
        );
    }
}