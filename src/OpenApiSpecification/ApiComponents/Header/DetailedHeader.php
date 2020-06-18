<?php declare(strict_types=1);

namespace App\OpenApiSpecification\ApiComponents\Header;

use App\OpenApiSpecification\ApiComponents\Example;
use App\OpenApiSpecification\ApiComponents\Examples;
use App\OpenApiSpecification\ApiComponents\Header;

/**
 * Describes a single operation header.
 * A unique Header is defined by a combination of a name and location.
 * http://spec.openapis.org/oas/v3.0.3#header-object
 */

abstract class DetailedHeader extends Header
{
    protected HeaderIsRequired $isRequired;
    protected HeaderIsDeprecated $isDeprecated;
    protected ?HeaderDescription $description;
    protected ?Example $example;
    protected ?Examples $examples;

    protected function __construct(
        ?HeaderIsRequired $isRequired,
        ?HeaderIsDeprecated $isDeprecated,
        ?HeaderDescription $description,
        ?HeaderDocName $docName,
        ?Example $example,
        ?Examples $examples
    ) {
        $this->isRequired = $isRequired ?? HeaderIsRequired::generateFalse();
        $this->isDeprecated = $isDeprecated ?? HeaderIsDeprecated::generateFalse();
        $this->description = $description;
        $this->docName = $docName;
        $this->example = $example;
        $this->examples = $examples;
    }

    public function isRequired(): bool
    {
        return $this->isRequired->toBool();
    }

    public function toDetailedHeader(): self
    {
        return $this;
    }

    public abstract function require();

    public abstract function deprecate();

    public abstract function setDescription(string $description);

    public abstract function addExample(Example $example);

    public abstract function setExample(Example $example);
}