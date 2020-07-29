<?php declare(strict_types=1);

namespace App\OpenApiSpecification\ApiComponents\ComponentsSchema;

use App\Message\FieldMessage;
use App\Message\Message;
use App\OpenApiSpecification\ApiComponents\ComponentsExample;
use App\OpenApiSpecification\ApiComponents\ComponentsSchema\Schema\SchemaDescription;
use App\OpenApiSpecification\ApiComponents\ComponentsSchema\Schema\SchemaIsDeprecated;
use App\OpenApiSpecification\ApiComponents\ComponentsSchema\Schema\SchemaIsNullable;
use App\OpenApiSpecification\ApiComponents\ComponentsSchema\Schema\SchemaIsRequired;
use App\OpenApiSpecification\ApiComponents\ComponentsSchema\Schema\SchemaMaximum;
use App\OpenApiSpecification\ApiComponents\ComponentsSchema\Schema\SchemaMinimum;
use App\OpenApiSpecification\ApiComponents\ComponentsSchema\Schema\SchemaName;
use App\OpenApiSpecification\ApiComponents\ComponentsSchema\Schema\SchemaType;
use App\OpenApiSpecification\ApiException\SpecificationException;

final class IntegerSchema extends PrimitiveSchema
{
    protected ?SchemaName $name;
    private ?SchemaDescription $description;
    private ?SchemaMinimum $minimum;
    private ?SchemaMaximum $maximum;

    private function __construct(
        SchemaType $type,
        SchemaIsRequired $isRequired,
        ?SchemaName $name = null,
        ?SchemaDescription $description = null,
        ?ComponentsExample $example = null,
        ?SchemaMinimum $minimum = null,
        ?SchemaMaximum $maximum = null,
        ?SchemaIsNullable $isNullable = null,
        ?SchemaIsDeprecated $isDeprecated = null
    ) {
        $this->type = $type;
        $this->isRequired = $isRequired;
        $this->isDeprecated = $isDeprecated ?? SchemaIsDeprecated::generateFalse();
        $this->name = $name;
        $this->description = $description;
        $this->example = $example;
        if ($minimum && $maximum && ($minimum->toInt() > $maximum->toInt())) {
            throw SpecificationException::generateMinimumShouldBeLessThanMaximum();
        }
        $this->minimum = $minimum;
        $this->maximum = $maximum;
        $this->isNullable = $isNullable ?? SchemaIsNullable::generateFalse();
    }

    public function setName(string $name): self
    {
        $this->name = SchemaName::fromString($name);
        return $this;
    }

    public function require(): self
    {
        $this->isRequired = SchemaIsRequired::generateTrue();
        return $this;
    }

    public function unRequire(): self
    {
        $this->isRequired = SchemaIsRequired::generateFalse();
        return $this;
    }

    public function deprecate(): self
    {
        $this->isDeprecated = SchemaIsDeprecated::generateTrue();
        return $this;
    }

    public function setFormat(string $format): self
    {
        $this->type = $this->type->setFormat($format);
        return $this;
    }

    public function setDescription(string $description): self
    {
        $this->description = SchemaDescription::fromString($description);
        return $this;
    }

    public function setExample(ComponentsExample $example): self
    {
        $exception = $this->validateValue($example->toExample()->getLiteralValue());
        if ($exception) {
            throw $exception;
        }

        $this->example = $example;
        return $this;
    }

    private function areValueSettingsValid(?SchemaMinimum $minimum, ?SchemaMaximum $maximum): bool
    {
        if (!$minimum || !$maximum) {
            return true;
        }

        return $minimum->toInt() <= $maximum->toInt();
    }

    public function setMinimum(int $minimum): self
    {
        $minimum = SchemaMinimum::fromInt($minimum);
        if (!$this->areValueSettingsValid($minimum, $this->maximum)) {
            throw SpecificationException::generateMinimumShouldBeLessThanMaximum();
        }
        $this->minimum = $minimum;
        return $this;
    }

    public function setMaximum(int $maximum): self
    {
        $maximum = SchemaMaximum::fromInt($maximum);
        if (!$this->areValueSettingsValid($this->minimum, $maximum)) {
            throw SpecificationException::generateMinimumShouldBeLessThanMaximum();
        }
        $this->maximum = $maximum;
        return $this;
    }

    public function makeNullable(): self
    {
        $this->isNullable = SchemaIsNullable::generateTrue();
        return $this;
    }

    public static function generate(): self
    {
        return new self(SchemaType::generateInteger(), SchemaIsRequired::generateFalse());
    }

    public function getMinimum(): ?SchemaMinimum
    {
        return $this->minimum;
    }

    public function getMaximum(): ?SchemaMaximum
    {
        return $this->maximum;
    }

    private function isIntegerValueValid(int $value): ?Message
    {
        if ($this->minimum && $value < $this->minimum->toInt()) {
            return Message::generateError(
                'less_than_minimum',
                "Supplied value $value, minimum allowed is: " . $this->minimum->toInt(),
                [
                    '%correctMinimum%' => (string)$this->minimum->toInt(),
                    '%suppliedValue%' => (string)$value
                ]
            );
        }
        if ($this->maximum && $value > $this->maximum->toInt()) {
            return Message::generateError(
                'more_than_maximum_length',
                "Supplied value $value, maximum allowed is: " . $this->maximum->toInt(),
                [
                    '%correctMaximum%' => (string)$this->maximum->toInt(),
                    '%suppliedValue%' => (string)$value
                ]
            );
        }
        return null;
    }

    public function isValueValid($value): array
    {
        $errors = [];
        if ($this->isNullable->toBool() && is_null($value)) {
            return [];
        }

        if (!is_int($value)) {
            $errorMessage = $this->getWrongTypeMessage('integer', $value);
            $errors[] = $this->name ?
                FieldMessage::generate([$this->name->toString()], $errorMessage) :
                $errorMessage;
            return $errors;
        }


        $integerValueError = $this->isIntegerValueValid($value);
        if ($integerValueError) {
            $errors[] = $this->name ?
                FieldMessage::generate([$this->name->toString()], $integerValueError) :
                $integerValueError;
        }

        return $errors;
    }

    public function toOpenApiSpecification(): array
    {
        $specification = ['type' => $this->type->getType()];
        if ($this->type->hasFormat()) {
            $specification['format'] = $this->type->getFormat();
        }
        if ($this->description) {
            $specification['description'] = $this->description->toString();
        }
        if ($this->minimum) {
            $specification['minimum'] = $this->minimum->toInt();
        }
        if ($this->maximum) {
            $specification['maximum'] = $this->maximum->toInt();
        }
        if ($this->isNullable()) {
            $specification['nullable'] = true;
        }
        if ($this->isDeprecated->toBool()) {
            $specification['deprecated'] = true;
        }
        if ($this->example) {
            $specification['example'] = $this->example->getLiteralValue();
        }
        return $specification;
    }

    public function getValueFromTrimmedCastedString(string $value): int
    {
        return (int)$value;
    }
}