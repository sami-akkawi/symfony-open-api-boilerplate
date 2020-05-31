<?php declare(strict=1);

namespace App\ApiV1Bundle\ApiSpecification\ApiComponents\Parameter;

use App\ApiV1Bundle\ApiSpecification\ApiComponents\Schema\IntegerSchema;

final class IntegerParameter extends SchemaParameter
{
    private static function generate(string $name, ParameterLocation $location): self
    {
        return new self(
            ParameterName::fromString($name),
            $location,
            $location->isInPath() ? ParameterIsRequired::generateTrue() : ParameterIsRequired::generateFalse(),
            ParameterIsDeprecated::generateFalse(),
            IntegerSchema::generate()
        );
    }

    public static function generateInCookie(string $name, ?string $docName = null): self
    {
        return self::generate($name, ParameterLocation::generateCookie());
    }

    public static function generateInQuery(string $name, ?string $docName = null): self
    {
        return self::generate($name, ParameterLocation::generateQuery());
    }

    public static function generateInHeader(string $name, ?string $docName = null): self
    {
        return self::generate($name, ParameterLocation::generateHeader());
    }

    public static function generateInPath(string $name, ?string $docName = null): self
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
            $this->description,
            $this->docName
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
            $this->description,
            $this->docName
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
            $this->description,
            $this->docName
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
            ParameterDescription::fromString($description),
            $this->docName
        );
    }

    public function makeNullable(): self
    {
        return new self(
            $this->name,
            $this->location,
            $this->isRequired,
            $this->isDeprecated,
            $this->schema->makeNullable(),
            $this->description,
            $this->docName
        );
    }

    public function setMinimum(int $minimum): self
    {
        return new self(
            $this->name,
            $this->location,
            $this->isRequired,
            $this->isDeprecated,
            $this->schema->setMinimum($minimum),
            $this->description,
            $this->docName
        );
    }

    public function setMaximum(int $maximum): self
    {
        return new self(
            $this->name,
            $this->location,
            $this->isRequired,
            $this->isDeprecated,
            $this->schema->setMaximum($maximum),
            $this->description,
            $this->docName
        );
    }

    public function setDocName(string $name): self
    {
        return new self(
            $this->name,
            $this->location,
            $this->isRequired,
            $this->isDeprecated,
            $this->schema,
            $this->description,
            ParameterDocName::fromString($name)
        );
    }
}