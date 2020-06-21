<?php declare(strict_types=1);

namespace App\OpenApiSpecification\ApiComponents\Response;

use App\Message\FieldMessage;
use App\Message\Message;
use App\OpenApiSpecification\ApiComponents\MediaType;
use App\OpenApiSpecification\ApiComponents\MediaTypes;
use App\OpenApiSpecification\ApiComponents\Response\Response\ResponseDescription;
use App\OpenApiSpecification\ApiComponents\Response\Response\ResponseHttpCode;
use App\OpenApiSpecification\ApiComponents\Response\Response\ResponseName;
use App\OpenApiSpecification\ApiComponents\Schema;
use App\OpenApiSpecification\ApiComponents\Response;

/**
 * Describes a single response from an API Operation, including design-time, static links to operations based on the
 * response.
 * http://spec.openapis.org/oas/v3.0.3#responses-object
 */

final class DetailedResponse extends Response
{
    private ResponseDescription $description;
    private MediaTypes $content;

    private function __construct(
        ResponseHttpCode $code,
        ResponseDescription $description,
        MediaTypes $content,
        ?ResponseName $name = null
    )
    {
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
            MediaTypes::generate()
                ->addMediaType(MediaType::generateJson($schema))
                ->addMediaType(MediaType::generateXml($schema))
        );
    }

    public static function generateJsonCreated(Schema $schema): self
    {
        return new self(
            ResponseHttpCode::generateOk(),
            ResponseDescription::fromString('Created.'),
            MediaTypes::generate()
                ->addMediaType(MediaType::generateJson($schema))
                ->addMediaType(MediaType::generateXml($schema))
        );
    }

    public static function generateNotFoundResponse(Schema $schema): self
    {
        return new self(
            ResponseHttpCode::generateNotFound(),
            ResponseDescription::fromString('Not Found.'),
            MediaTypes::generate()
                ->addMediaType(MediaType::generateJson($schema))
                ->addMediaType(MediaType::generateXml($schema))
        );
    }

    public static function generateUnprocessableEntityResponse(Schema $schema): self
    {
        return new self(
            ResponseHttpCode::generateUnprocessableEntity(),
            ResponseDescription::fromString('Unprocessable Entity.'),
            MediaTypes::generate()
                ->addMediaType(MediaType::generateJson($schema))
                ->addMediaType(MediaType::generateXml($schema))
        );
    }

    public static function generateCorruptDataResponse(Schema $schema): self
    {
        return new self(
            ResponseHttpCode::generateCorruptData(),
            ResponseDescription::fromString('Corrupt Data. The server fetched data that do not comply with the defined schema.'),
            MediaTypes::generate()
                ->addMediaType(MediaType::generateJson($schema))
                ->addMediaType(MediaType::generateXml($schema))
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