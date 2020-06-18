<?php declare(strict=1);

namespace App\OpenApiSpecification\ApiPath;

use App\OpenApiSpecification\ApiComponents\Parameters;
use App\OpenApiSpecification\ApiComponents\RequestBody;
use App\OpenApiSpecification\ApiComponents\Responses;
use App\OpenApiSpecification\ApiPath\PathOperation\OperationDescription;
use App\OpenApiSpecification\ApiPath\PathOperation\OperationHasOptionalSecurity;
use App\OpenApiSpecification\ApiPath\PathOperation\OperationId;
use App\OpenApiSpecification\ApiPath\PathOperation\OperationIsDeprecated;
use App\OpenApiSpecification\ApiPath\PathOperation\OperationName;
use App\OpenApiSpecification\ApiPath\PathOperation\OperationSummary;
use App\OpenApiSpecification\ApiPath\PathOperation\OperationTags;

/**
 * Describes the operations available on a single path. A Path Item MAY be empty, due to ACL constraints.
 * The path itself is still exposed to the documentation viewer but they will not know which operations and parameters
 * are available.
 * http://spec.openapis.org/oas/v3.0.3#path-item-object
 */

final class PathOperation
{
    private OperationName $name;
    private Parameters $parameters;
    private Responses $responses;
    private OperationId $id;
    private OperationTags $tags;
    private OperationIsDeprecated $isDeprecated;
    private OperationHasOptionalSecurity $hasOptionalSecurity;
    private ?OperationDescription $description;
    private ?OperationSummary $summary;
    private ?RequestBody $requestBody;

    private function __construct(
        OperationName $name,
        OperationId $id,
        OperationTags $tags,
        Parameters $parameters,
        Responses $responses,
        OperationIsDeprecated $isDeprecated,
        OperationHasOptionalSecurity $hasOptionalSecurity,
        ?OperationDescription $description = null,
        ?OperationSummary $summary = null,
        ?RequestBody $requestBody = null
    ) {
        $this->name = $name;
        $this->id = $id;
        $this->tags = $tags;
        $this->parameters = $parameters;
        $this->responses = $responses;
        $this->isDeprecated = $isDeprecated;
        $this->hasOptionalSecurity = $hasOptionalSecurity;
        $this->description = $description;
        $this->summary = $summary;
        $this->requestBody = $requestBody;
    }

    public static function generate(
        OperationName $name,
        OperationId $id,
        Parameters $parameters,
        Responses $responses
    ): self {
        return new self(
            $name,
            $id,
            OperationTags::generate(),
            $parameters,
            $responses,
            OperationIsDeprecated::generate(),
            OperationHasOptionalSecurity::generate()
        );
    }

    private function isDeprecated(): bool
    {
        return $this->isDeprecated->toBool();
    }

    private function hasOptionalSecurity(): bool
    {
        return $this->hasOptionalSecurity->toBool();
    }

    public function setDescription(OperationDescription $description): self
    {
        return new self(
            $this->name,
            $this->id,
            $this->tags,
            $this->parameters,
            $this->responses,
            $this->isDeprecated,
            $this->hasOptionalSecurity,
            $description,
            $this->summary,
            $this->requestBody
        );
    }

    public function setSummary(OperationSummary $summary): self
    {
        return new self(
            $this->name,
            $this->id,
            $this->tags,
            $this->parameters,
            $this->responses,
            $this->isDeprecated,
            $this->hasOptionalSecurity,
            $this->description,
            $summary,
            $this->requestBody
        );
    }

    public function getName(): OperationName
    {
        return $this->name;
    }

    public function getId(): OperationId
    {
        return $this->id;
    }

    public function addTags(OperationTags $tags): self
    {
        return new self(
            $this->name,
            $this->id,
            $this->tags->addTags($tags),
            $this->parameters,
            $this->responses,
            $this->isDeprecated,
            $this->hasOptionalSecurity,
            $this->description,
            $this->summary,
            $this->requestBody
        );
    }

    public function setRequestBody(RequestBody $requestBody): self
    {
        return new self(
            $this->name,
            $this->id,
            $this->tags,
            $this->parameters,
            $this->responses,
            $this->isDeprecated,
            $this->hasOptionalSecurity,
            $this->description,
            $this->summary,
            $requestBody
        );
    }

    public function deprecate(): self
    {
        return new self(
            $this->name,
            $this->id,
            $this->tags,
            $this->parameters,
            $this->responses,
            $this->isDeprecated->setTrue(),
            $this->hasOptionalSecurity,
            $this->description,
            $this->summary,
            $this->requestBody
        );
    }

    public function setSecurityOptional(): self
    {
        return new self(
            $this->name,
            $this->id,
            $this->tags,
            $this->parameters,
            $this->responses,
            $this->isDeprecated,
            $this->hasOptionalSecurity->setTrue(),
            $this->description,
            $this->summary,
            $this->requestBody
        );
    }

    public function toOpenApiSpecification(): array
    {
        $specification = [
            'operationId' => $this->id->toString(),
            'responses' => $this->responses->toOpenApiSpecificationForEndpoint()
        ];
        if ($this->parameters->isDefined()) {
            $specification['parameters'] = $this->parameters->toOpenApiSpecificationForEndpoint();
        }
        if ($this->requestBody) {
            $specification['requestBody'] = $this->requestBody->toOpenApiSpecification();
        }
        if ($this->summary) {
            $specification['summary'] = $this->summary->toString();
        }
        if ($this->description) {
            $specification['description'] = $this->description->toString();
        }
        if ($this->tags->isDefined()) {
            $specification['tags'] = $this->tags->toArray();
        }
        if ($this->isDeprecated()) {
            $specification['deprecated'] = true;
        }
        if ($this->hasOptionalSecurity()) {
            $specification['security'] = [];
        }

        return $specification;
    }
}