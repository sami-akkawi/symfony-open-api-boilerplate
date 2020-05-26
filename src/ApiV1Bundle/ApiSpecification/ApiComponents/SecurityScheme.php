<?php declare(strict=1);
// Created by sami-akkawi on 10.05.20

namespace App\ApiV1Bundle\ApiSpecification\ApiComponents;

use App\ApiV1Bundle\ApiSpecification\ApiComponents\ComponentsSecurityScheme\SecurityScheme\SchemeDescription;
use App\ApiV1Bundle\ApiSpecification\ApiComponents\ComponentsSecurityScheme\SecurityScheme\SchemeName;
use App\ApiV1Bundle\ApiSpecification\ApiComponents\ComponentsSecurityScheme\SecurityScheme\SchemeType;

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