<?php declare(strict_types=1);

namespace App\OpenApiSpecification\ApiComponents\ComponentsLink;

use App\OpenApiSpecification\ApiComponents\ComponentsLink;
use App\OpenApiSpecification\ApiComponents\ComponentsLink\Link\LinkDescription;
use App\OpenApiSpecification\ApiComponents\ComponentsLink\Link\LinkKey;
use App\OpenApiSpecification\ApiComponents\ComponentsLink\Link\LinkOperationId;
use App\OpenApiSpecification\ApiComponents\ComponentsLink\Link\LinkParameter;
use App\OpenApiSpecification\ApiComponents\ComponentsLink\Link\LinkParameters;
use App\OpenApiSpecification\ApiPath\PathOperation\OperationId;
use App\OpenApiSpecification\ApiServer;

final class Link extends ComponentsLink
{
    private LinkOperationId $operationId;
    private LinkParameters $parameters;
    private ?LinkDescription $description;
    private ?ApiServer $server;
    private ?LinkKey $key;

    private function __construct(
        LinkOperationId $operationId,
        LinkParameters $parameters,
        ?LinkDescription $description = null,
        ?ApiServer $server = null,
        ?LinkKey $key = null
    ) {
        $this->operationId = $operationId;
        $this->parameters = $parameters;
        $this->description = $description;
        $this->server = $server;
        $this->key = $key;
    }

    public static function generateForOperationId(OperationId $id): self
    {
        return new self(
            LinkOperationId::fromOperationId($id),
            LinkParameters::generateEmpty()
        );
    }

    public function addLinkParameter(LinkParameter $parameter): self
    {
        return new self(
            $this->operationId,
            $this->parameters->addParameter($parameter),
            $this->description,
            $this->server,
            $this->key
        );
    }

    public function setDescription(string $description): self
    {
        return new self(
            $this->operationId,
            $this->parameters,
            LinkDescription::fromString($description),
            $this->server,
            $this->key
        );
    }

    public function setServer(ApiServer $server): self
    {
        return new self(
            $this->operationId,
            $this->parameters,
            $this->description,
            $server,
            $this->key
        );
    }

    public function getKey(): ?LinkKey
    {
        return $this->key;
    }

    public function setKey(string $key): self
    {
        return new self(
            $this->operationId,
            $this->parameters,
            $this->description,
            $this->server,
            LinkKey::fromString($key)
        );
    }

    public function getParameters(): LinkParameters
    {
        return $this->parameters;
    }

    public function getOperationId(): LinkOperationId
    {
        return $this->operationId;
    }

    public function toOpenApiSpecification(): array
    {
        $specifications = [
            'operationId' => $this->operationId->toString(),
        ];
        if ($this->server) {
            $specifications['server'] = $this->server->toOpenApiSpecification();
        }
        if ($this->description) {
            $specifications['description'] = $this->description->toString();
        }
        if ($this->parameters->isDefined()) {
            $specifications['parameters'] = $this->parameters->toOpenApiSpecification();
        }

        return $specifications;
    }

    public function toLink(): Link
    {
        return $this;
    }
}