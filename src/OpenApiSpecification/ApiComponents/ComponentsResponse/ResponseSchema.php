<?php declare(strict_types=1);

namespace App\OpenApiSpecification\ApiComponents\ComponentsResponse;

use App\Message\FieldMessage;
use App\Message\Message;
use App\OpenApiSpecification\ApiComponents\ComponentsLink;
use App\OpenApiSpecification\ApiComponents\ComponentsLinks;
use App\OpenApiSpecification\ApiComponents\ComponentsMediaType;
use App\OpenApiSpecification\ApiComponents\ComponentsMediaTypes;
use App\OpenApiSpecification\ApiComponents\ComponentsResponse;
use App\OpenApiSpecification\ApiComponents\ComponentsResponse\Response\ResponseDescription;
use App\OpenApiSpecification\ApiComponents\ComponentsResponse\Response\ResponseHttpCode;
use App\OpenApiSpecification\ApiComponents\ComponentsResponse\Response\ResponseName;
use App\OpenApiSpecification\ApiComponents\ComponentsSchema;
use App\OpenApiSpecification\ApiComponents\ComponentsSchema\Schema;

/**
 * Describes a single response from an API Operation, including design-time, static links to operations based on the
 * response.
 * http://spec.openapis.org/oas/v3.0.3#responses-object
 */

final class ResponseSchema extends ComponentsResponse
{
    private ResponseDescription $description;
    private ComponentsMediaTypes $content;
    private ComponentsLinks $links;

    private function __construct(
        ResponseHttpCode $code,
        ResponseDescription $description,
        ComponentsMediaTypes $content,
        ComponentsLinks $links,
        ?ResponseName $name = null
    ) {
        $this->code = $code;
        $this->description = $description;
        $this->content = $content;
        $this->links = $links;
        $this->name = $name;
    }

    private static function generate(
        ResponseHttpCode $code,
        ResponseDescription $description,
        ComponentsSchema $schema
    ): self {
        return new self(
            $code,
            $description,
            ComponentsMediaTypes::generate()
                ->addMediaType(ComponentsMediaType::generateJson($schema))
                ->addMediaType(ComponentsMediaType::generateXml($schema)),
            ComponentsLinks::generate()
        );
    }

    public static function generateOk(ComponentsSchema $schema): self
    {
        return static::generate(
            ResponseHttpCode::generateOk(),
            ResponseDescription::fromString('Ok.'),
            $schema
        );
    }

    public static function generateCreated(ComponentsSchema $schema): self
    {
        return static::generate(
            ResponseHttpCode::generateCreated(),
            ResponseDescription::fromString('Created.'),
            $schema
        );
    }

    public static function generateBadRequestResponse(ComponentsSchema $schema): self
    {
        return static::generate(
            ResponseHttpCode::generateBadRequest(),
            ResponseDescription::fromString('Bad Request.'),
            $schema
        );
    }

    public static function generateUnauthorizedResponse(ComponentsSchema $schema): self
    {
        return static::generate(
            ResponseHttpCode::generateUnauthorized(),
            ResponseDescription::fromString('Unauthorized. Please login.'),
            $schema
        );
    }

    public static function generateForbiddenResponse(ComponentsSchema $schema): self
    {
        return static::generate(
            ResponseHttpCode::generateForbidden(),
            ResponseDescription::fromString('Forbidden. Not enough permissions.'),
            $schema
        );
    }

    public static function generateNotFoundResponse(ComponentsSchema $schema): self
    {
        return static::generate(
            ResponseHttpCode::generateNotFound(),
            ResponseDescription::fromString('Not Found.'),
            $schema
        );
    }

    public static function generateUnprocessableEntityResponse(ComponentsSchema $schema): self
    {
        return static::generate(
            ResponseHttpCode::generateUnprocessableEntity(),
            ResponseDescription::fromString('Unprocessable Entity.'),
            $schema
        );
    }

    public static function generateCorruptDataResponse(ComponentsSchema $schema): self
    {
        return static::generate(
            ResponseHttpCode::generateCorruptData(),
            ResponseDescription::fromString('Corrupt Data. The server fetched data that do not comply with the defined schema.'),
            $schema
        );
    }

    public function setName(string $name): self
    {
        return new self($this->code, $this->description, $this->content, $this->links, ResponseName::fromString($name));
    }

    public function toOpenApiSpecification(): array
    {
        $specifications = [
            'description' => $this->description->toString(),
            'content' => $this->content->toOpenApiSpecification()
        ];
        if ($this->links->isDefined()) {
            $specifications['links'] = $this->links->toOpenApiSpecificationForEndpoint();
        }

        return $specifications;
    }

    public function toResponseSchema(): ResponseSchema
    {
        return $this;
    }

    public function getSchemaByMimeType(string $mimeType): ?Schema
    {
        return $this->content->getSchemaByMimeType($mimeType);
    }

    public function getLinks(): ComponentsLinks
    {
        return $this->links;
    }

    public function getDefinedMimeTypes(): array
    {
        return $this->content->getMimeTypes();
    }

    public function addLink(ComponentsLink $link): self
    {
        return new self(
            $this->code,
            $this->description,
            $this->content,
            $this->links->addLink($link),
            $this->name
        );
    }

    public function isValueValidByMimeType(string $mimeType, $value): array
    {
        $mediaType = $this->content->getByMimeType($mimeType);
        if (!$mimeType) {
            $message = Message::generateError(
                'mime_not_defined_as_response',
                "$mimeType not as potential response",
                ['%submittedMimeType%' => $mimeType]
            );
            if ($this->name) {
                return [FieldMessage::generate([$this->name->toString()], $message)];
            }
            return [$message];
        }

        return $mediaType->isValueValid($value, []);
    }

    public function getDescription(): ResponseDescription
    {
        return $this->description;
    }
}