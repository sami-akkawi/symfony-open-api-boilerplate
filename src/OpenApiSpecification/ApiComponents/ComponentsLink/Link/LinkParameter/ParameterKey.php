<?php declare(strict_types=1);

namespace App\OpenApiSpecification\ApiComponents\ComponentsLink\Link\LinkParameter;

use App\OpenApiSpecification\ApiComponents\ComponentsLink\Link\LinkParameter\ParameterKey\KeyLocation;
use App\OpenApiSpecification\ApiComponents\ComponentsLink\Link\LinkParameter\ParameterKey\KeyName;

final class ParameterKey
{
    private KeyLocation $location;
    private KeyName $name;

    private function __construct(KeyLocation $location, KeyName $name)
    {
        $this->location = $location;
        $this->name = $name;
    }

    public static function generatePath(string $name): self
    {
        return new self(KeyLocation::generatePath(), KeyName::fromString($name));
    }

    public static function generateQuery(string $name): self
    {
        return new self(KeyLocation::generateQuery(), KeyName::fromString($name));
    }

    public static function generateHeader(string $name): self
    {
        return new self(KeyLocation::generateHeader(), KeyName::fromString($name));
    }

    public static function generateCookie(string $name): self
    {
        return new self(KeyLocation::generateCookie(), KeyName::fromString($name));
    }

    public static function generateRequestBody(string $name): self
    {
        return new self(KeyLocation::generateBody(), KeyName::fromString($name));
    }

    public function getLocation(): KeyLocation
    {
        return $this->location;
    }

    public function getName(): KeyName
    {
        return $this->name;
    }

    public function isIdenticalTo(self $key): bool
    {
        return (
            $this->getLocation()->isIdenticalTo($key->getLocation())
            && $this->getName()->isIdenticalTo($key->getName())
        );
    }

    public function toOpenApiSpecification(): string
    {
        return $this->location->toOpenApiSpecification() . $this->name->toOpenApiSpecification();
    }
}