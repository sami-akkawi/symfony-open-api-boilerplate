<?php declare(strict=1);

namespace App\ApiV1Bundle\ApiSpecification\ApiComponents\ComponentsResponse\Response\ResponseContent;

use App\ApiV1Bundle\ApiSpecification\ApiComponents\ComponentsResponse\Response\ResponseContent\MediaType\MediaTypeMimeType;
use App\ApiV1Bundle\ApiSpecification\ApiComponents\ComponentsSchema\ObjectSchema;
use App\ApiV1Bundle\ApiSpecification\ApiComponents\ComponentsSchema\ReferenceSchema;

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