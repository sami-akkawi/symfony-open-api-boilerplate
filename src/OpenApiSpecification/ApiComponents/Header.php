<?php declare(strict_types=1);

namespace App\OpenApiSpecification\ApiComponents;

use App\OpenApiSpecification\ApiComponents\Header\DetailedHeader;
use App\OpenApiSpecification\ApiComponents\Header\HeaderDocName;

abstract class Header
{
    protected ?HeaderDocName $docName;

    public abstract function setDocName(string $name);

    public abstract function toDetailedHeader(): DetailedHeader;

    public function hasDocName(): bool
    {
        return (bool)$this->docName;
    }

    public function getDocName(): ?HeaderDocName
    {
        return $this->docName;
    }

    public abstract function toOpenApiSpecification(): array;
}