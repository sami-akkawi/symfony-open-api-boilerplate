<?php declare(strict=1);

namespace App\ApiV1Bundle\ApiSpecification\ApiComponents\Parameter;

use App\ApiV1Bundle\ApiSpecification\ApiComponents\Parameter;
use App\ApiV1Bundle\ApiSpecification\ApiException\SpecificationException;

/**
 * Describes a single operation parameter.
 * A unique parameter is defined by a combination of a name and location.
 * http://spec.openapis.org/oas/v3.0.3#parameter-object
 */

abstract class DetailedParameter extends Parameter
{
    private const INVALID_NAMES_IN_HEADER = ['Accept', 'Content-Type', 'Authorization'];

    protected ParameterName $name;
    protected ParameterLocation $location;
    protected ParameterIsRequired $isRequired;
    protected ParameterIsDeprecated $isDeprecated;
    protected ?ParameterDescription $description;
    // todo: protected ?ParameterStyle $style;
    // todo: protected ?Example $example;
    // todo: protected ?Examples $examples;

    protected function __construct(
        ParameterName $name,
        ParameterLocation $location,
        ParameterIsRequired $isRequired,
        ParameterIsDeprecated $isDeprecated,
        ?ParameterDescription $description,
        ?ParameterDocName $docName
    ) {
        if ($location->isInHeader() && in_array($name->toString(), self::INVALID_NAMES_IN_HEADER)) {
            SpecificationException::generateInvalidNameInHeader($name->toString());
        }

        $this->name = $name;
        $this->location = $location;
        $this->isRequired = $isRequired;
        $this->isDeprecated = $isDeprecated;
        $this->description = $description;
        $this->docName = $docName;
    }

    public function getName(): ParameterName
    {
        return $this->name;
    }

    public function getLocation(): ParameterLocation
    {
        return $this->location;
    }

    public function isInPath(): bool
    {
        return $this->location->isInPath();
    }

    public abstract static function generateInQuery(string $name);

    public abstract static function generateInHeader(string $name);

    public abstract static function generateInPath(string $name);

    public abstract static function generateInCookie(string $name);

    public abstract function require();

    public abstract function deprecate();

    public abstract function setDescription(string $description);
}