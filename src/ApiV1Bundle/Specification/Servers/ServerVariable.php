<?php declare(strict=1);

namespace App\ApiV1Bundle\Specification\Servers;

use App\ApiV1Bundle\Specification\Exception\SpecificationException;
use App\ApiV1Bundle\Specification\Servers\ServerVariable\VariableDefaultValue;
use App\ApiV1Bundle\Specification\Servers\ServerVariable\VariableDescription;
use App\ApiV1Bundle\Specification\Servers\ServerVariable\VariableName;
use App\ApiV1Bundle\Specification\Servers\ServerVariable\VariableValueOptions;

/**
 * An object representing a Server Variable for server URL template substitution.
 * https://swagger.io/specification/#server-variable-object
 */

final class ServerVariable
{
    private VariableName $name;
    private VariableDefaultValue $defaultValue;
    private ?VariableDescription $description;
    private ?VariableValueOptions $valueOptions;

    private function __construct(
        VariableName $name,
        VariableDefaultValue $defaultValue,
        ?VariableDescription $description = null,
        ?VariableValueOptions $valueOptions = null
    ) {
        $this->name = $name;
        $this->defaultValue = $defaultValue;
        $this->description = $description;
        $this->valueOptions = $valueOptions;
    }

    public static function generate(string $name, string $default): self
    {
        return new self(
            VariableName::fromString($name),
            VariableDefaultValue::fromString($default)
        );
    }

    public function getName(): VariableName
    {
        return $this->name;
    }

    public function setDescription(string $description): self
    {
        return new self(
            $this->name,
            $this->defaultValue,
            VariableDescription::fromString($description),
            $this->valueOptions
        );
    }

    public function setOptions(array $options): self
    {
        if (!in_array($this->defaultValue->toString(), $options)) {
            throw SpecificationException::generateEnumNotValidException($this->defaultValue->toString());
        }

        return new self(
            $this->name,
            $this->defaultValue,
            $this->description,
            VariableValueOptions::fromArray($options)
        );
    }

    public function addOptions(array $options): self
    {
        return new self(
            $this->name,
            $this->defaultValue,
            $this->description,
            VariableValueOptions::fromArray(array_merge($options, [$this->defaultValue->toString()]))
        );
    }

    public function toOpenApiSpecification(): array
    {
        $specification = [
            'default' => $this->defaultValue->toString()
        ];
        if ($this->description) {
            $specification['description'] = $this->description->toString();
        }
        if ($this->valueOptions) {
            $specification['enum'] = $this->valueOptions->toArray();
        }
        return $specification;
    }
}