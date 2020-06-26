<?php declare(strict_types=1);

namespace App\OpenApiSpecification\ApiComponents\ComponentsRequestBody;

use App\Message\FieldMessage;
use App\Message\Message;
use App\OpenApiSpecification\ApiComponents\ComponentsMediaType;
use App\OpenApiSpecification\ApiComponents\ComponentsMediaTypes;
use App\OpenApiSpecification\ApiComponents\ComponentsRequestBody;
use App\OpenApiSpecification\ApiComponents\ComponentsRequestBody\RequestBody\RequestBodyDescription;
use App\OpenApiSpecification\ApiComponents\ComponentsRequestBody\RequestBody\RequestBodyIsRequired;
use App\OpenApiSpecification\ApiComponents\ComponentsRequestBody\RequestBody\RequestBodyName;
use App\OpenApiSpecification\ApiComponents\ComponentsSchema;

/**
 * Describes a single request body.
 * http://spec.openapis.org/oas/v3.0.3#request-body-object
 */

final class RequestBody extends ComponentsRequestBody
{
    private ComponentsMediaTypes $content;
    private RequestBodyIsRequired $isRequired;
    private ?RequestBodyDescription $description;

    private function __construct(
        ComponentsMediaTypes $content,
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
        return new self(ComponentsMediaTypes::generate());
    }

    public static function generate(ComponentsSchema $schema): self
    {
        return new self(ComponentsMediaTypes::generate()
            ->addMediaType(ComponentsMediaType::generateJson($schema))
            ->addMediaType(ComponentsMediaType::generateXml($schema)));
    }

    public function addMediaType(ComponentsMediaType $mediaType): self
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

    public function getSchemaByMimeType(string $mimeType): ComponentsSchema
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

    public function toRequestBody(): RequestBody
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