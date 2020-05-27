<?php declare(strict=1);

namespace App\ApiV1Bundle\ApiSpecification\ApiComponents\ComponentsResponse;

use App\ApiV1Bundle\ApiSpecification\ApiComponents\ComponentsResponse\Response\ResponseContent;
use App\ApiV1Bundle\ApiSpecification\ApiComponents\ComponentsResponse\Response\ResponseContent\ContentMediaType;
use App\ApiV1Bundle\ApiSpecification\ApiComponents\ComponentsResponse\Response\ResponseDescription;
use App\ApiV1Bundle\ApiSpecification\ApiComponents\ComponentsResponse\Response\ResponseHttpCode;
use App\ApiV1Bundle\ApiSpecification\ApiComponents\ComponentsResponse\Response\ResponseKey;
use App\ApiV1Bundle\ApiSpecification\ApiComponents\ComponentsSchema\ObjectSchema;
use App\ApiV1Bundle\ApiSpecification\ApiComponents\ComponentsSchema\ReferenceSchema;
use App\ApiV1Bundle\ApiSpecification\ApiComponents\Response;

/**
 * Describes a single response from an API Operation, including design-time, static links to operations based on the
 * response.
 * http://spec.openapis.org/oas/v3.0.3#responses-object
 */

final class DetailedResponse extends Response
{
    private ResponseDescription $description;
    private ResponseContent $content;

    private function __construct(
        ResponseHttpCode $code,
        ResponseDescription $description,
        ResponseContent $content,
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
            ResponseContent::generate()->addMediaType(
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
            ResponseHttpCode::generateOk(),
            ResponseDescription::fromString('404 Not Found. Resource not found.'),
            ResponseContent::generate()->addMediaType(
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