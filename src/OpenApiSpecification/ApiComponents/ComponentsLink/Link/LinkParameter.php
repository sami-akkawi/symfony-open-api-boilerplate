<?php declare(strict_types=1);

namespace App\OpenApiSpecification\ApiComponents\ComponentsLink\Link;

use App\OpenApiSpecification\ApiComponents\ComponentsLink\Link\LinkParameter\ParameterKey;
use App\OpenApiSpecification\ApiComponents\ComponentsLink\Link\LinkParameter\ParameterKey\KeyLocation;
use App\OpenApiSpecification\ApiComponents\ComponentsLink\Link\LinkParameter\ParameterKey\KeyName;
use App\OpenApiSpecification\ApiComponents\ComponentsLink\Link\LinkParameter\ParameterValue;
use App\OpenApiSpecification\ApiComponents\ComponentsLink\Link\LinkParameter\ParameterValue\ValueLocation;
use App\OpenApiSpecification\ApiComponents\ComponentsLink\Link\LinkParameter\ParameterValue\ValueName;
use App\OpenApiSpecification\ApiComponents\ComponentsLink\Link\LinkParameter\ParameterValue\ValueSource;

final class LinkParameter
{
    private ParameterKey $key;
    private ParameterValue $value;

    private function __construct(ParameterKey $key, ParameterValue $value)
    {
        $this->key = $key;
        $this->value = $value;
    }

    public static function generate(ParameterKey $key, ParameterValue $value): self
    {
        return new self($key, $value);
    }

    public function getKey(): ParameterKey
    {
        return $this->key;
    }

    public function getKeyLocation(): KeyLocation
    {
        return $this->key->getLocation();
    }

    public function getKeyName(): KeyName
    {
        return $this->key->getName();
    }

    public function getValue(): ParameterValue
    {
        return $this->value;
    }

    public function getValueLocation(): ValueLocation
    {
        return $this->value->getLocation();
    }

    public function getValueName(): ValueName
    {
        return $this->value->getName();
    }

    public function getValueSource(): ValueSource
    {
        return $this->value->getSource();
    }

    public function toOpenApiSpecification(): array
    {
        return [$this->key->toOpenApiSpecification() => $this->value->toOpenApiSpecification()];
    }
}