<?php declare(strict=1);

namespace App\ApiV1Bundle\ApiSpecification\ApiComponents\RequestBody;

use App\ApiV1Bundle\ApiSpecification\ApiComponents\MediaType;
use App\ApiV1Bundle\ApiSpecification\ApiComponents\MediaTypes;
use App\ApiV1Bundle\ApiSpecification\ApiComponents\RequestBody;

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

    public static function generate(): self
    {
        return new self(MediaTypes::generate());
    }

    public function addMediaType(MediaType $mediaType): self
    {
        return new self($this->content->addMediaType($mediaType), $this->isRequired, $this->description, $this->name);
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

    public function toOpenApi3Specification(): array
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