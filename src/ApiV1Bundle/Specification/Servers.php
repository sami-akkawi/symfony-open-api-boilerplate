<?php declare(strict=1);

namespace App\ApiV1Bundle\Specification;

use App\ApiV1Bundle\Specification\Exception\SpecificException;
use App\ApiV1Bundle\Specification\Servers\ServerUrl;

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
            throw SpecificException::generateDuplicateDefinitionException($server->getUrl()->toString());
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