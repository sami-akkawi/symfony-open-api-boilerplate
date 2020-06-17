<?php declare(strict=1);

namespace App\ApiV1Bundle\ApiSpecification\ApiComponents;

use App\ApiV1Bundle\ApiSpecification\ApiComponents\Header\DetailedHeader;
use App\ApiV1Bundle\ApiSpecification\ApiComponents\Header\HeaderDocName;

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