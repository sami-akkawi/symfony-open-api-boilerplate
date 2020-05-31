<?php declare(strict=1);

namespace App\ApiV1Bundle\ApiSpecification\ApiComponents\Content;

use App\ApiV1Bundle\ApiSpecification\ApiComponents\Content\MediaType\MediaTypeMimeType;
use App\ApiV1Bundle\ApiSpecification\ApiComponents\Schema\ObjectSchema;
use App\ApiV1Bundle\ApiSpecification\ApiComponents\Schema\ReferenceSchema;

final class ContentMediaType
{
    private MediaTypeMimeType $mimeType;
    /** @var ObjectSchema|ReferenceSchema $schema */
    private $schema;

    private function __construct(MediaTypeMimeType $mimeType, $schema)
    {
        $this->mimeType = $mimeType;
        $this->schema = $schema;
    }

    /**
     * @param ObjectSchema|ReferenceSchema $schema
     */
    public static function generateJson($schema): self
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