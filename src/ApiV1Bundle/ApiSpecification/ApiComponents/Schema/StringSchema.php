<?php declare(strict=1);

namespace App\ApiV1Bundle\ApiSpecification\ApiComponents\Schema;

use App\ApiV1Bundle\ApiSpecification\ApiComponents\Example;
use App\ApiV1Bundle\ApiSpecification\ApiComponents\Schema\Schema\SchemaDescription;
use App\ApiV1Bundle\ApiSpecification\ApiComponents\Schema\Schema\SchemaIsDeprecated;
use App\ApiV1Bundle\ApiSpecification\ApiComponents\Schema\Schema\SchemaIsNullable;
use App\ApiV1Bundle\ApiSpecification\ApiComponents\Schema\Schema\SchemaIsRequired;
use App\ApiV1Bundle\ApiSpecification\ApiComponents\Schema\Schema\SchemaMaximumLength;
use App\ApiV1Bundle\ApiSpecification\ApiComponents\Schema\Schema\SchemaMinimumLength;
use App\ApiV1Bundle\ApiSpecification\ApiComponents\Schema\Schema\SchemaName;
use App\ApiV1Bundle\ApiSpecification\ApiComponents\Schema\Schema\SchemaType;
use App\ApiV1Bundle\ApiSpecification\ApiException\SpecificationException;

final class StringSchema extends PrimitiveSchema
{
    private SchemaType $type;
    protected ?SchemaName $name;
    private ?SchemaDescription $description;
    private ?SchemaMinimumLength $minimumLength;
    private ?SchemaMaximumLength $maximumLength;

    private function __construct(
        SchemaType $type,
        SchemaIsRequired $isRequired,
        ?SchemaName $name = null,
        ?SchemaDescription $description = null,
        ?Example $example = null,
        ?SchemaIsNullable $isNullable = null,
        ?SchemaMinimumLength $minimumLength = null,
        ?SchemaMaximumLength $maximumLength = null,
        ?SchemaIsDeprecated $isDeprecated = null
    ) {
        $this->type = $type;
        $this->isRequired = $isRequired;
        $this->isDeprecated = $isDeprecated ?? SchemaIsDeprecated::generateFalse();
        $this->name = $name;
        $this->description = $description;
        $this->example = $example;
        $this->isNullable = $isNullable ?? SchemaIsNullable::generateFalse();
        $this->minimumLength = $minimumLength;
        $this->maximumLength = $maximumLength;
    }

    public function setName(string $name): self
    {
        return new self(
            $this->type,
            $this->isRequired,
            SchemaName::fromString($name),
            $this->description,
            $this->example,
            $this->isNullable,
            $this->minimumLength,
            $this->maximumLength,
            $this->isDeprecated
        );
    }

    public function require(): self
    {
        return new self(
            $this->type,
            SchemaIsRequired::generateTrue(),
            $this->name,
            $this->description,
            $this->example,
            $this->isNullable,
            $this->minimumLength,
            $this->maximumLength,
            $this->isDeprecated
        );
    }

    public function deprecate(): self
    {
        return new self(
            $this->type,
            $this->isRequired,
            $this->name,
            $this->description,
            $this->example,
            $this->isNullable,
            $this->minimumLength,
            $this->maximumLength,
            SchemaIsDeprecated::generateTrue()
        );
    }

    public static function generate(): self
    {
        return new self(SchemaType::generateString(), SchemaIsRequired::generateFalse());
    }

    public function setFormat(string $format): self
    {
        return new self(
            $this->type->setFormat($format),
            $this->isRequired,
            $this->name,
            $this->description,
            $this->example,
            $this->isNullable,
            $this->minimumLength,
            $this->maximumLength,
            $this->isDeprecated
        );
    }

    public function setOptions(array $options): self
    {
        return new self(
            $this->type->setEnum($options),
            $this->isRequired,
            $this->name,
            $this->description,
            $this->example,
            $this->isNullable,
            $this->minimumLength,
            $this->maximumLength,
            $this->isDeprecated
        );
    }

    public function setDescription(string $description): self
    {
        return new self(
            $this->type,
            $this->isRequired,
            $this->name,
            SchemaDescription::fromString($description),
            $this->example,
            $this->isNullable,
            $this->minimumLength,
            $this->maximumLength,
            $this->isDeprecated
        );
    }

    public function setExample(Example $example): self
    {
        $exception = $this->validateValue($example->toDetailedExample()->toMixed());
        if ($exception) {
            throw $exception;
        }

        return new self(
            $this->type,
            $this->isRequired,
            $this->name,
            $this->description,
            $example,
            $this->isNullable,
            $this->minimumLength,
            $this->maximumLength,
            $this->isDeprecated
        );
    }

    public function makeNullable(): self
    {
        return new self(
            $this->type,
            $this->isRequired,
            $this->name,
            $this->description,
            $this->example,
            SchemaIsNullable::generateTrue(),
            $this->minimumLength,
            $this->maximumLength,
            $this->isDeprecated
        );
    }

    private function areLengthSettingsValid(
        ?SchemaMinimumLength $minimumLength,
        ?SchemaMaximumLength $maximumLength
    ): bool {
        if (!$minimumLength || !$maximumLength) {
            return true;
        }

        if ($minimumLength->toInt() > $maximumLength->toInt()) {
            return false;
        }

        return true;
    }

    public function setMinimumLength(int $minLength): self
    {
        if ($minLength < 0) {
            throw SpecificationException::generateMinimumLengthCannotBeLessThanZero();
        }

        $minimumLength = SchemaMinimumLength::fromInt($minLength);
        if (!$this->areLengthSettingsValid($minimumLength, $this->maximumLength)) {
            throw SpecificationException::generateMinimumShouldBeLessThanMaximum();
        }

        return new self(
            $this->type,
            $this->isRequired,
            $this->name,
            $this->description,
            $this->example,
            $this->isNullable,
            $minimumLength,
            $this->maximumLength,
            $this->isDeprecated
        );
    }

    public function setMaximumLength(int $maxLength): self
    {
        $maximumLength = SchemaMaximumLength::fromInt($maxLength);
        if (!$this->areLengthSettingsValid($this->minimumLength, $maximumLength)) {
            throw SpecificationException::generateMinimumShouldBeLessThanMaximum();
        }

        return new self(
            $this->type,
            $this->isRequired,
            $this->name,
            $this->description,
            $this->example,
            $this->isNullable,
            $this->minimumLength,
            $maximumLength,
            $this->isDeprecated
        );
    }

    public function isStringLengthValid(string $value): ?string
    {
        $stringLength = strlen($value);
        if ($this->minimumLength && $value < $this->minimumLength->toInt()) {
            return "$value has length $stringLength, minimum allowed is: " . $this->minimumLength->toInt();
        }
        if ($this->maximumLength && $value > $this->maximumLength->toInt()) {
            return "$value has length $stringLength, maximum allowed is: " . $this->maximumLength->toInt();
        }

        return null;
    }

    public function isValueValid($value): array
    {
        if (!is_string($value)) {
            return [$this->getWrongTypeMessage('string', $value)];
        }

        $errors = [];
        $lengthError = $this->isStringLengthValid($value);
        if ($lengthError) {
            $errors[] = $lengthError;
        }

        $typeError = $this->type->isStringValueValid($value);
        if ($typeError) {
            $errors[] = $typeError;
        }

        return $errors;
    }

    private function getNullableStringExample(): ?string
    {
        if ($this->example) {
            return $this->example->toMixed();
        }

        if ($this->type->isAtomTime()) {
            return '2020-05-14T18:53:23+02:00';
        }

        if ($this->type->isUrl()) {
            return 'https://www.openapis.org/';
        }

        return null;
    }

    public function toOpenApiSpecification(): array
    {
        $example = $this->getNullableStringExample();
        $specification = ['type' => $this->type->getType()];
        if ($this->type->hasFormat()) {
            $specification['format'] = $this->type->getFormat();
        }
        if ($this->type->isEnum()) {
            $specification['enum'] = $this->type->getEnum();
        }
        if ($this->description) {
            $specification['description'] = $this->description->toString();
        }
        if ($this->minimumLength) {
            $specification['minLength'] = $this->minimumLength->toInt();
        }
        if ($this->maximumLength) {
            $specification['maxLength'] = $this->maximumLength->toInt();
        }
        if ($example) {
            $specification['example'] = $example;
        }
        if ($this->isNullable()) {
            $specification['nullable'] = true;
        }
        return $specification;
    }
}