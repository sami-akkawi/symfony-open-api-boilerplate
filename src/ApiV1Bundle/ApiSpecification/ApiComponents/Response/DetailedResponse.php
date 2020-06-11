<?php declare(strict=1);

namespace App\ApiV1Bundle\ApiSpecification\ApiComponents\Response;

use App\ApiV1Bundle\ApiSpecification\ApiComponents\ResponseContent;
use App\ApiV1Bundle\ApiSpecification\ApiComponents\ResponseContent\ContentMediaType;
use App\ApiV1Bundle\ApiSpecification\ApiComponents\Response\Response\ResponseDescription;
use App\ApiV1Bundle\ApiSpecification\ApiComponents\Response\Response\ResponseHttpCode;
use App\ApiV1Bundle\ApiSpecification\ApiComponents\Response\Response\ResponseName;
use App\ApiV1Bundle\ApiSpecification\ApiComponents\Schema;
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
        ?ResponseName $name = null
    ) {
        $this->code = $code;
        $this->description = $description;
        $this->content = $content;
        $this->name = $name;
    }

    public static function generateOkJson(Schema $schema): self
    {
        return new self(
            ResponseHttpCode::generateOk(),
            ResponseDescription::fromString('Ok.'),
            ResponseContent::generate()->addMediaType(ContentMediaType::generateJson($schema))
        );
    }

    public static function generateNotFoundJson(Schema $schema): self
    {
        return new self(
            ResponseHttpCode::generateNotFound(),
            ResponseDescription::fromString('Not Found.'),
            ResponseContent::generate()->addMediaType(ContentMediaType::generateJson($schema))
        );
    }

    public function setName(string $name): self
    {
        return new self($this->code, $this->description, $this->content, ResponseName::fromString($name));
    }

    public function toOpenApiSpecification(): array
    {
        return [
            'description' => $this->description->toString(),
            'content' => $this->content->toOpenApiSpecification()
        ];
    }

    public function toDetailedResponse(): DetailedResponse
    {
        return $this;
    }
}