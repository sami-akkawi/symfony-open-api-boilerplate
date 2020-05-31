<?php declare(strict=1);

namespace App\ApiV1Bundle\ApiSpecification\ApiComponents\Response;

use App\ApiV1Bundle\ApiSpecification\ApiComponents\Content;
use App\ApiV1Bundle\ApiSpecification\ApiComponents\Content\ContentMediaType;
use App\ApiV1Bundle\ApiSpecification\ApiComponents\Response\Response\ResponseDescription;
use App\ApiV1Bundle\ApiSpecification\ApiComponents\Response\Response\ResponseHttpCode;
use App\ApiV1Bundle\ApiSpecification\ApiComponents\Response\Response\ResponseKey;
use App\ApiV1Bundle\ApiSpecification\ApiComponents\Schema\ObjectSchema;
use App\ApiV1Bundle\ApiSpecification\ApiComponents\Schema\ReferenceSchema;
use App\ApiV1Bundle\ApiSpecification\ApiComponents\Response;

/**
 * Describes a single response from an API Operation, including design-time, static links to operations based on the
 * response.
 * http://spec.openapis.org/oas/v3.0.3#responses-object
 */

final class DetailedResponse extends Response
{
    private ResponseDescription $description;
    private Content $content;

    private function __construct(
        ResponseHttpCode $code,
        ResponseDescription $description,
        Content $content,
        ?ResponseKey $key = null
    ) {
        $this->code = $code;
        $this->description = $description;
        $this->content = $content;
        $this->key = $key;
    }

    /**
     * @param ObjectSchema|ReferenceSchema $schema
     */
    public static function generateOkJson($schema): self
    {
        return new self(
            ResponseHttpCode::generateOk(),
            ResponseDescription::fromString('Ok.'),
            Content::generate()->addMediaType(
                ContentMediaType::generateJson($schema)
            )
        );
    }

    /**
     * @param ObjectSchema|ReferenceSchema $schema
     */
    public static function generateNotFoundJson($schema): self
    {
        return new self(
            ResponseHttpCode::generateNotFound(),
            ResponseDescription::fromString('Not Found.'),
            Content::generate()->addMediaType(
                ContentMediaType::generateJson($schema)
            )
        );
    }

    public function setKey(ResponseKey $key): self
    {
        return new self($this->code, $this->description, $this->content, $key);
    }

    public function toOpenApiSpecification(): array
    {
        return [
            'description' => $this->description->toString(),
            'content' => $this->content->toOpenApi3Specification()
        ];
    }
}