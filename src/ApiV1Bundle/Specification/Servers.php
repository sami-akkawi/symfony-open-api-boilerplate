<?php declare(strict=1);

namespace App\ApiV1Bundle\Specification;

use App\ApiV1Bundle\Specification\Exception\SpecificationException;
use App\ApiV1Bundle\Specification\Servers\ServerUrl;

/**
 * An array of Server Objects, which provide connectivity information to a target server.
 * If the servers property is not provided, or is an empty array, the default value would be a Server
 * Object with a url value of /.
 * https://swagger.io/specification/#fixed-fields
 */

final class Servers
{
    /** @var Server[] */
    private array $servers;

    private function __construct(array $servers)
    {
        $this->servers = $servers;
    }

    public static function generate(): self
    {
        return new self([]);
    }

    private function hasServer(ServerUrl $url): bool
    {
        foreach ($this->servers as $server) {
            if ($server->getUrl()->isIdenticalTo($url)) {
                return true;
            }
        }
        return false;
    }

    public function addServer(Server $server): self
    {
        if ($this->hasServer($server->getUrl())) {
            throw SpecificationException::generateDuplicateDefinitionException($server->getUrl()->toString());
        }

        return new self(array_merge($this->servers, [$server]));
    }

    public function isDefined(): bool
    {
        return (bool)count($this->servers);
    }

    public function toOpenApiSpecification(): array
    {
        $specifications = [];

        foreach ($this->servers as $server) {
            $specifications[] = $server->toOpenApiSpecification();
        }

        return $specifications;
    }
}