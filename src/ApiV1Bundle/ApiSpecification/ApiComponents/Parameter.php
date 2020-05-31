<?php declare(strict=1);

namespace App\ApiV1Bundle\ApiSpecification\ApiComponents;

use App\ApiV1Bundle\ApiSpecification\ApiComponents\Parameter\DetailedParameter;
use App\ApiV1Bundle\ApiSpecification\ApiComponents\Parameter\ParameterDocName;
use App\ApiV1Bundle\ApiSpecification\ApiComponents\Parameter\ReferenceParameter;
use App\ApiV1Bundle\Parameter\AbstractParameter;

/**
 * Describes a single operation parameter.
 * A unique parameter is defined by a combination of a name and location.
 * http://spec.openapis.org/oas/v3.0.3#parameter-object
 */

abstract class Parameter
{
    private const PARAMETERS_NAMESPACE_PREFIX = 'App\\ApiV1Bundle\\ApiSpecification\\ApiComponents\\Parameter\\';

    protected ?ParameterDocName $docName;

    public abstract function setDocName(string $name);

    public function hasDocName(): bool
    {
        return (bool)$this->docName;
    }

    public function getDocName(): ?ParameterDocName
    {
        return $this->docName;
    }

    private function getDetailedParameterFromReferenceParameter(ReferenceParameter $parameter): DetailedParameter
    {
        /** @var AbstractParameter $namespace */
        $namespace = (self::PARAMETERS_NAMESPACE_PREFIX . $parameter->getName());
        return $namespace::getOpenApiParameter();
    }

    public function isIdenticalTo(self $parameter): bool
    {
        if ($parameter instanceof ReferenceParameter) {
            $thatParameter = $this->getDetailedParameterFromReferenceParameter($parameter);
        } else {
            /** @var DetailedParameter $thatParameter */
            $thatParameter = $parameter;
        }

        if (static::class === ReferenceParameter::class) {
            /** @var ReferenceParameter $thisReferenceParameter */
            $thisReferenceParameter = $this;
            $thisParameter = $this->getDetailedParameterFromReferenceParameter($thisReferenceParameter);
        } else {
            /** @var DetailedParameter $thisParameter */
            $thisParameter = $this;
        }

        return (
            $thisParameter->getName()->isIdenticalTo($thatParameter->getName())
            && $thisParameter->getLocation()->isIdenticalTo($thatParameter->getLocation())
        );
    }

    public abstract function toOpenApiSpecification(): array;
}