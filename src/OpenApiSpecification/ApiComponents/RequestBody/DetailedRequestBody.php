<?php declare(strict_types=1);

namespace App\OpenApiSpecification\ApiComponents\RequestBody;

use App\Message\FieldMessage;
use App\Message\Message;
use App\OpenApiSpecification\ApiComponents\MediaType;
use App\OpenApiSpecification\ApiComponents\MediaTypes;
use App\OpenApiSpecification\ApiComponents\RequestBody;
use App\OpenApiSpecification\ApiComponents\Schema;

/**
 * Describes a single request body.
 * http://spec.openapis.org/oas/v3.0.3#request-body-object
 */

final class DetailedRequestBody extends RequestBody
{
    private MediaTypes $content;
    private RequestBodyIsRequired $isRequired;
    private ?RequestBodyDescription $description;

    private function __construct(
        MediaTypes $content,
        ?RequestBodyIsRequired $isRequired = null,
        ?RequestBodyDescription $description = null,
        ?RequestBodyName $name = null
    ) {
        $this->content = $content;
        $this->isRequired = $isRequired ?? RequestBodyIsRequired::generateFalse();
        $this->description = $description;
        $this->name = $name;
    }

    public static function generateEmpty(): self
    {
        return new self(MediaTypes::generate());
    }

    public static function generate(Schema $schema): self
    {
        return new self(MediaTypes::generate()
            ->addMediaType(MediaType::generateJson($schema))
            ->addMediaType(MediaType::generateXml($schema)));
    }

    public function addMediaType(MediaType $mediaType): self
    {
        return new self($this->content->addMediaType($mediaType), $this->isRequired, $this->description, $this->name);
    }

    public function isValueValidByMimeType(string $mimeType, $value): array
    {
        $mediaType = $this->content->getByMimeType($mimeType);
        if (!$mimeType) {
            $message = Message::generateError(
                'mime_not_supported_for_request_body',
                "$mimeType not supported in request body",
                ['%submittedMimeType%' => $mimeType]
            );
            if ($this->name) {
                return [FieldMessage::generate([$this->name->toString()], $message)];
            }
            return [$message];
        }

        return $mediaType->isValueValid($value);
    }

    public function getSchemaByMimeType(string $mimeType): Schema
    {
        return $this->content->getByMimeType($mimeType)->getSchema();
    }

    public function getDefinedMimeTypes(): array
    {
        return $this->content->getMimeTypes();
    }

    public function setName(string $name): self
    {
        return new self($this->content, $this->isRequired, $this->description, RequestBodyName::fromString($name));
    }

    public function toDetailedRequestBody(): DetailedRequestBody
    {
        return $this;
    }

    public function require(): self
    {
        return new self($this->content, RequestBodyIsRequired::generateTrue(), $this->description, $this->name);
    }

    public function setDescription(string $description): self
    {
        return new self($this->content, $this->isRequired, RequestBodyDescription::fromString($description), $this->name);
    }

    public function toOpenApiSpecification(): array
    {
        $specifications=[];
        if ($this->isRequired->toBool()) {
            $specifications['required'] = true;
        }
        if ($this->description) {
            $specifications['description'] = $this->description->toString();
        }
        $specifications['content'] = $this->content->toOpenApiSpecification();

        return $specifications;
    }
}