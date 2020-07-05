<?php declare(strict_types=1);

namespace App\OpenApiSpecification\ApiComponents\ComponentsSchema;

use App\Message\FieldMessage;
use App\Message\Message;
use App\OpenApiSpecification\ApiComponents\ComponentsExample;
use App\OpenApiSpecification\ApiComponents\ComponentsSchema\Schema\SchemaDescription;
use App\OpenApiSpecification\ApiComponents\ComponentsSchema\Schema\SchemaIsDeprecated;
use App\OpenApiSpecification\ApiComponents\ComponentsSchema\Schema\SchemaIsNullable;
use App\OpenApiSpecification\ApiComponents\ComponentsSchema\Schema\SchemaIsRequired;
use App\OpenApiSpecification\ApiComponents\ComponentsSchema\Schema\SchemaMaximumLength;
use App\OpenApiSpecification\ApiComponents\ComponentsSchema\Schema\SchemaMinimumLength;
use App\OpenApiSpecification\ApiComponents\ComponentsSchema\Schema\SchemaName;
use App\OpenApiSpecification\ApiComponents\ComponentsSchema\Schema\SchemaType;
use App\OpenApiSpecification\ApiException\SpecificationException;
use Symfony\Component\Uid\Uuid;

final class StringSchema extends PrimitiveSchema
{
    protected ?SchemaName $name;
    private ?SchemaDescription $description;
    private ?SchemaMinimumLength $minimumLength;
    private ?SchemaMaximumLength $maximumLength;

    private function __construct(
        SchemaType $type,
        SchemaIsRequired $isRequired,
        ?SchemaName $name = null,
        ?SchemaDescription $description = null,
        ?ComponentsExample $example = null,
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

    public function unRequire(): self
    {
        return new self(
            $this->type,
            SchemaIsRequired::generateFalse(),
            $this->name,
            $this->description,
            $this->example,
            $this->isNullable,
            $this->minimumLength,
            $this->maximumLength,
            $this->isDeprecated
        );
    }

    public static function generate(): self
    {
        return new self(SchemaType::generateString(), SchemaIsRequired::generateFalse());
    }

    public static function generateUuid(): self
    {
        return new self(SchemaType::generateUuidString(), SchemaIsRequired::generateFalse());
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

    public function setExample(ComponentsExample $example): self
    {
        $exception = $this->validateValue($example->toExample()->getLiteralValue());
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

    public function isValueLengthValid(string $value): ?Message
    {
        $stringLength = strlen($value);
        if ($this->minimumLength && $stringLength < $this->minimumLength->toInt()) {
            return Message::generateError(
                'less_than_minimum_length',
                "String has length $stringLength, minimum allowed is: " . $this->minimumLength->toInt(),
                [
                    '%correctMinimum%' => (string)$this->minimumLength->toInt(),
                    '%suppliedMinimum%' => (string)$stringLength
                ]
            );
        }
        if ($this->maximumLength && $stringLength > $this->maximumLength->toInt()) {
            return Message::generateError(
                'more_than_maximum_length',
                "String has length $stringLength, maximum allowed is: " . $this->maximumLength->toInt(),
                [
                    '%correctMaximum%' => (string)$this->maximumLength->toInt(),
                    '%suppliedMaximum%' => (string)$stringLength
                ]
            );
        }

        return null;
    }

    public function getMaximumLength(): ?SchemaMaximumLength
    {
        return $this->maximumLength;
    }

    public function getMinimumLength(): ?SchemaMinimumLength
    {
        return $this->minimumLength;
    }

    public function getValueFromTrimmedCastedString(string $value): string
    {
        $decodedString = json_decode($value);
        return $decodedString ?? $value;
    }

    public function isValueValid($value): array
    {
        if ($this->isNullable->toBool() && is_null($value)) {
            return [];
        }
        $errors = [];
        if (!is_string($value)) {
            $errorMessage = $this->getWrongTypeMessage('string', $value);
            $errors[] = $this->name ?
                FieldMessage::generate([$this->name->toString()], $errorMessage) :
                $errorMessage;
            return $errors;
        }

        $lengthError = $this->isValueLengthValid($value);
        if ($lengthError) {
            $errors[] = $this->name ?
                FieldMessage::generate([$this->name->toString()], $lengthError) :
                $lengthError;
        }

        $typeError = $this->type->isStringValueValid($value);
        if ($typeError) {
            $errors[] = $this->name ?
                FieldMessage::generate([$this->name->toString()], $typeError) :
                $typeError;
        }

        return $errors;
    }

    private function getNullableStringExample(): ?string
    {
        if ($this->example) {
            return $this->example->getLiteralValue();
        }

        if ($this->type->isAtomTime()) {
            return '2018-11-19T08:53:23+02:00';
        }

        if ($this->type->isStringUrl()) {
            return 'https://www.easycharter.ch/';
        }

        if ($this->type->isStringTime()) {
            return '06:45:21';
        }

        if ($this->type->isStringUuid()) {
            return Uuid::v4()->toRfc4122();
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
        if ($this->type->isStringEnum()) {
            $specification['enum'] = $this->type->getEnum();
        }
        if ($this->description) {
            $specification['description'] = $this->description->toString();
        }
        if ($this->isNullable()) {
            $specification['nullable'] = true;
        }
        if ($this->isDeprecated->toBool()) {
            $specification['deprecated'] = true;
        }
        if ($example) {
            $specification['example'] = $example;
        }
        return $specification;
    }
}