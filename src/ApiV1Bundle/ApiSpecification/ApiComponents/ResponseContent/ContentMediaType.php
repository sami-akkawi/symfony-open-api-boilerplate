<?php declare(strict=1);

namespace App\ApiV1Bundle\ApiSpecification\ApiComponents\ResponseContent;

use App\ApiV1Bundle\ApiSpecification\ApiComponents\ResponseContent\MediaType\MediaTypeMimeType;
use App\ApiV1Bundle\ApiSpecification\ApiComponents\Schema;

final class ContentMediaType
{
    private MediaTypeMimeType $mimeType;
    private Schema $schema;

    private function __construct(MediaTypeMimeType $mimeType, Schema $schema)
    {
        $this->mimeType = $mimeType;
        $this->schema = $schema;
    }

    public static function generateJson(Schema $schema): self
    {
        return new self(MediaTypeMimeType::generateJson(), $schema);
    }

    public function getMimeType(): MediaTypeMimeType
    {
        return $this->mimeType;
    }

    public function toOpenApiSpecification(): array
    {
        return ['schema' => $this->schema->toOpenApiSpecification()];
    }
}