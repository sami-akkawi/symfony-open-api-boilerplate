<?php declare(strict_types=1);

namespace App\OpenApiSpecification\ApiComponents\ComponentsResponse;

use App\Message\FieldMessage;
use App\Message\Message;
use App\OpenApiSpecification\ApiComponents\ComponentsMediaType;
use App\OpenApiSpecification\ApiComponents\ComponentsMediaTypes;
use App\OpenApiSpecification\ApiComponents\ComponentsResponse;
use App\OpenApiSpecification\ApiComponents\ComponentsResponse\Response\ResponseDescription;
use App\OpenApiSpecification\ApiComponents\ComponentsResponse\Response\ResponseHttpCode;
use App\OpenApiSpecification\ApiComponents\ComponentsResponse\Response\ResponseName;
use App\OpenApiSpecification\ApiComponents\Schema;

/**
 * Describes a single response from an API Operation, including design-time, static links to operations based on the
 * response.
 * http://spec.openapis.org/oas/v3.0.3#responses-object
 */

final class ResponseSchema extends ComponentsResponse
{
    private ResponseDescription $description;
    private ComponentsMediaTypes $content;

    private function __construct(
        ResponseHttpCode $code,
        ResponseDescription $description,
        ComponentsMediaTypes $content,
        ?ResponseName $name = null
    ) {
        $this->code = $code;
        $this->description = $description;
        $this->content = $content;
        $this->name = $name;
    }

    public static function generateOk(Schema $schema): self
    {
        return new self(
            ResponseHttpCode::generateOk(),
            ResponseDescription::fromString('Ok.'),
            ComponentsMediaTypes::generate()
                ->addMediaType(ComponentsMediaType::generateJson($schema))
                ->addMediaType(ComponentsMediaType::generateXml($schema))
        );
    }

    public static function generateJsonCreated(Schema $schema): self
    {
        return new self(
            ResponseHttpCode::generateOk(),
            ResponseDescription::fromString('Created.'),
            ComponentsMediaTypes::generate()
                ->addMediaType(ComponentsMediaType::generateJson($schema))
                ->addMediaType(ComponentsMediaType::generateXml($schema))
        );
    }

    public static function generateNotFoundResponse(Schema $schema): self
    {
        return new self(
            ResponseHttpCode::generateNotFound(),
            ResponseDescription::fromString('Not Found.'),
            ComponentsMediaTypes::generate()
                ->addMediaType(ComponentsMediaType::generateJson($schema))
                ->addMediaType(ComponentsMediaType::generateXml($schema))
        );
    }

    public static function generateUnprocessableEntityResponse(Schema $schema): self
    {
        return new self(
            ResponseHttpCode::generateUnprocessableEntity(),
            ResponseDescription::fromString('Unprocessable Entity.'),
            ComponentsMediaTypes::generate()
                ->addMediaType(ComponentsMediaType::generateJson($schema))
                ->addMediaType(ComponentsMediaType::generateXml($schema))
        );
    }

    public static function generateCorruptDataResponse(Schema $schema): self
    {
        return new self(
            ResponseHttpCode::generateCorruptData(),
            ResponseDescription::fromString('Corrupt Data. The server fetched data that do not comply with the defined schema.'),
            ComponentsMediaTypes::generate()
                ->addMediaType(ComponentsMediaType::generateJson($schema))
                ->addMediaType(ComponentsMediaType::generateXml($schema))
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

    public function toResponse(): ResponseSchema
    {
        return $this;
    }

    public function getDefinedMimeTypes(): array
    {
        return $this->content->getMimeTypes();
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

        return $mediaType->isValueValid($value);
    }

    public function getDescription(): ResponseDescription
    {
        return $this->description;
    }
}