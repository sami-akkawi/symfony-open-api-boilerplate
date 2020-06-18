<?php declare(strict=1);

namespace App\OpenApiSpecification\ApiComponents\SecurityScheme;

use App\OpenApiSpecification\ApiComponents\SecurityScheme\SecurityScheme\HttpBearerFormat;
use App\OpenApiSpecification\ApiComponents\SecurityScheme\SecurityScheme\HttpScheme;
use App\OpenApiSpecification\ApiComponents\SecurityScheme\SecurityScheme\SchemeDescription;
use App\OpenApiSpecification\ApiComponents\SecurityScheme\SecurityScheme\SchemeName;
use App\OpenApiSpecification\ApiComponents\SecurityScheme\SecurityScheme\SchemeType;
use App\OpenApiSpecification\ApiComponents\SecurityScheme;
use App\OpenApiSpecification\ApiException\SpecificationException;

final class HttpSecurityScheme extends SecurityScheme
{
    private HttpScheme $scheme;
    private ?HttpBearerFormat $format;

    private function __construct(SchemeName $schemeName, HttpScheme $scheme, ?SchemeDescription $description = null, ?HttpBearerFormat
    $format = null)
    {
        parent::__construct($schemeName, SchemeType::generateHttp(), $description);
        $this->scheme = $scheme;
        if ($format && !$this->scheme->isBearer()) {
            throw SpecificationException::cannotSetFormatToNonBearerHttpScheme();
        }
        $this->format = $format;
    }

    public static function generateBasic(string $name): self
    {
        return new self(SchemeName::fromString($name), HttpScheme::generateBasic());
    }

    public static function generateBearer(string $name): self
    {
        return new self(SchemeName::fromString($name), HttpScheme::generateBearer());
    }

    public static function generateDigest(string $name): self
    {
        return new self(SchemeName::fromString($name), HttpScheme::generateDigest());
    }

    public static function generateOAuth(string $name): self
    {
        return new self(SchemeName::fromString($name), HttpScheme::generateOAuth());
    }

    public function setDescription(string $description): self
    {
        return new self(
            $this->schemeName,
            $this->scheme,
            SchemeDescription::fromString($description),
            $this->format
        );
    }

    public function setBearerFormat(string $format): self
    {
        return new self($this->schemeName, $this->scheme, $this->description, HttpBearerFormat::fromString($format));
    }

    public function toOpenApiSpecification(): array
    {
        $specification = [
            'type' => $this->type->toString(),
            'scheme' => $this->scheme->toString(),
        ];

        if ($this->format) {
            $specification['bearerFormat'] = $this->format->toString();
        }

        if ($this->description) {
            $specification['description'] = $this->description->toString();
        }

        return $specification;
    }
}