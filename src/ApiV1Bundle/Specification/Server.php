<?php declare(strict=1);

namespace App\ApiV1Bundle\Specification;

use App\ApiV1Bundle\Specification\Servers\ServerDescription;
use App\ApiV1Bundle\Specification\Servers\ServerUrl;
use App\ApiV1Bundle\Specification\Servers\ServerVariable;
use App\ApiV1Bundle\Specification\Servers\ServerVariables;

final class Server
{
    private ServerUrl $url;
    private ServerVariables $variables;
    private ?ServerDescription $description;

    private function __construct(ServerUrl $url, ServerVariables $variables, ?ServerDescription $description = null)
    {
        $this->url = $url;
        $this->variables = $variables;
        $this->description = $description;
    }

    public function getUrl(): ServerUrl
    {
        return $this->url;
    }

    public static function generate(string $url): self
    {
        return new self(ServerUrl::fromString($url), ServerVariables::generate());
    }

    public function setDescription(string $description): self
    {
        return new self(
            $this->url,
            $this->variables,
            ServerDescription::fromString($description)
        );
    }

    public function addVariable(ServerVariable $variable): self
    {
        return new self(
            $this->url,
            $this->variables->add($variable),
            $this->description
        );
    }

    public function toOpenApiSpecification(): array
    {
        $specification = [
            'url' => $this->url->toString()
        ];
        if ($this->description) {
            $specification['description'] = $this->description->toString();
        }
        if ($this->variables->hasVariables()) {
            $specification['variables'] = $this->variables->toOpenApiSpecification();
        }

        return $specification;
    }
}