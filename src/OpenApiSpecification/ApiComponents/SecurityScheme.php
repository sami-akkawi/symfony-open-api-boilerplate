<?php declare(strict_types=1);

namespace App\OpenApiSpecification\ApiComponents;

use App\OpenApiSpecification\ApiComponents\SecurityScheme\SecurityScheme\SchemeDescription;
use App\OpenApiSpecification\ApiComponents\SecurityScheme\SecurityScheme\SchemeName;
use App\OpenApiSpecification\ApiComponents\SecurityScheme\SecurityScheme\SchemeType;

abstract class SecurityScheme
{
    protected SchemeName $schemeName;
    protected SchemeType $type;
    protected ?SchemeDescription $description;

    protected function __construct(SchemeName $schemeName, SchemeType $type, ?SchemeDescription $description = null)
    {
        $this->schemeName = $schemeName;
        $this->type = $type;
        $this->description = $description;
    }

    public function getSchemeName(): SchemeName
    {
        return $this->schemeName;
    }

    public function getType(): SchemeType
    {
        return $this->type;
    }

    abstract function setDescription(string $description);

    abstract function toOpenApiSpecification(): array;
}