<?php declare(strict_types=1);

namespace App\OpenApiSpecification;

use App\OpenApiSpecification\ApiTag\TagDescription;
use App\OpenApiSpecification\ApiTag\TagExternalDocs;
use App\OpenApiSpecification\ApiTag\TagName;

/**
 * Adds metadata to a single tag that is used by the Operation Object. It is not mandatory to have a Tag Object per
 * tag defined in the Operation Object instances.
 * http://spec.openapis.org/oas/v3.0.3#tag-object
 */

final class ApiTag
{
    private TagName $name;
    private ?TagDescription $description;
    private ?TagExternalDocs $externalDocs;

    private function __construct(
        TagName $name,
        ?TagDescription $description = null,
        ?TagExternalDocs $externalDocs = null
    ) {
        $this->name = $name;
        $this->description = $description;
        $this->externalDocs = $externalDocs;
    }

    public static function generate(string $name): self
    {
        return new self(TagName::fromString($name));
    }

    public function getName(): TagName
    {
        return $this->name;
    }

    public function setDescription(string $description): self
    {
        return new self($this->name, TagDescription::fromString($description), $this->externalDocs);
    }

    public function setExternalDoc(TagExternalDocs $externalDocs): self
    {
        return new self($this->name, $this->description, $externalDocs);
    }

    public function toOpenApiSpecification(): array
    {
        $specification = [
            'name' => $this->name->toString()
        ];
        if ($this->description) {
            $specification['description'] = $this->description->toString();
        }
        if ($this->externalDocs) {
            $specification['externalDocs'] = $this->externalDocs->toOpenApi3Specification();
        }

        return $specification;
    }
}