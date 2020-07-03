<?php declare(strict_types=1);

namespace App\OpenApiSpecification\ApiComponents\ComponentsLink\Link\LinkParameter;

use App\OpenApiSpecification\ApiComponents\ComponentsLink\Link\LinkParameter\ParameterValue\ValueLocation;
use App\OpenApiSpecification\ApiComponents\ComponentsLink\Link\LinkParameter\ParameterValue\ValueName;
use App\OpenApiSpecification\ApiComponents\ComponentsLink\Link\LinkParameter\ParameterValue\ValueSource;
use App\OpenApiSpecification\ApiException\SpecificationException;

final class ParameterValue
{
    private ValueSource $source;
    private ValueLocation $location;
    private ValueName $name;

    private function __construct(ValueSource $source, ValueLocation $location, ValueName $name)
    {
        if ($source->isInResponse() && !$location->isInBody()) {
            throw SpecificationException::generateResponseParameterValueCanOnlyHaveBodyLocation();
        }

        $this->source = $source;
        $this->location = $location;
        $this->name = $name;
    }

    public static function generatePath(string $name): self
    {
        return new self(ValueSource::generateRequest(), ValueLocation::generatePath(), ValueName::fromString($name));
    }

    public static function generateQuery(string $name): self
    {
        return new self(ValueSource::generateRequest(), ValueLocation::generateQuery(), ValueName::fromString($name));
    }

    public static function generateCookie(string $name): self
    {
        return new self(ValueSource::generateRequest(), ValueLocation::generateCookie(), ValueName::fromString($name));
    }

    public static function generateHeader(string $name): self
    {
        return new self(ValueSource::generateRequest(), ValueLocation::generateHeader(), ValueName::fromString($name));
    }

    public static function generateRequestBody(string $name): self
    {
        return new self(ValueSource::generateRequest(), ValueLocation::generateBody(), ValueName::fromString($name));
    }

    public static function generateResponseBody(string $name): self
    {
        return new self(ValueSource::generateResponse(), ValueLocation::generateBody(), ValueName::fromString($name));
    }

    public function isInRequest(): bool
    {
        return $this->source->isInRequest();
    }

    public function isInResponse(): bool
    {
        return $this->source->isInResponse();
    }

    public function getName(): ValueName
    {
        return $this->name;
    }

    public function getLocation(): ValueLocation
    {
        return $this->location;
    }

    public function getSource(): ValueSource
    {
        return $this->source;
    }

    public function toOpenApiSpecification(): string
    {
        $specification = $this->location->toOpenApiSpecification() . $this->name->toOpenApiSpecification();
        if (!$this->location->isInBody()) {
            return $specification;
        }

        return $this->source->toOpenApiSpecification() . $specification;
    }
}