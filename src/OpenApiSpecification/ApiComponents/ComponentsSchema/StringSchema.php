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

    private function __construct(SchemaType $type)
    {
        $this->type = $type;
        $this->isRequired = SchemaIsRequired::generateFalse();
        $this->isDeprecated = SchemaIsDeprecated::generateFalse();
        $this->name = null;
        $this->description = null;
        $this->example = null;
        $this->isNullable = SchemaIsNullable::generateFalse();
        $this->minimumLength = null;
        $this->maximumLength = null;
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

    public static function generate(): self
    {
        return new self(SchemaType::generateString());
    }

    public static function generateUuid(): self
    {
        return new self(SchemaType::generateUuidString());
    }

    public static function generateUrl(): self
    {
        return new self(SchemaType::generateUrlString());
    }

    public static function generateUri(): self
    {
        return new self(SchemaType::generateUriString());
    }

    public static function generateBinary(): self
    {
        return new self(SchemaType::generateBinaryString());
    }

    public function isBinary(): bool
    {
        return $this->type->isBinary();
    }

    public function setFormat(string $format): self
    {
        $this->type = $this->type->setFormat($format);
        return $this;
    }

    public function setOptions(array $options): self
    {
        $this->type = $this->type->setEnum($options);
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

    public function makeNullable(): self
    {
        $this->isNullable = SchemaIsNullable::generateTrue();
        return $this;
    }

    public function deprecate(): self
    {
        $this->isDeprecated = SchemaIsDeprecated::generateTrue();
        return $this;
    }

    private function areLengthSettingsValid(?SchemaMinimumLength $minimumLength, ?SchemaMaximumLength $maximumLength): bool
    {
        if (!$minimumLength || !$maximumLength) {
            return true;
        }

        return $minimumLength->toInt() <= $maximumLength->toInt();
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

        $this->minimumLength = $minimumLength;
        return $this;
    }

    public function setMaximumLength(int $maxLength): self
    {
        if ($maxLength < 0) {
            throw SpecificationException::generateMaximumLengthCannotBeLessThanOrEqualToZero();
        }

        $maximumLength = SchemaMaximumLength::fromInt($maxLength);
        if (!$this->areLengthSettingsValid($this->minimumLength, $maximumLength)) {
            throw SpecificationException::generateMinimumShouldBeLessThanMaximum();
        }

        $this->maximumLength = $maximumLength;
        return $this;
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
            return 'https://www.your-website.com/';
        }

        if ($this->type->isStringTime()) {
            return '06:45:21';
        }

        if ($this->type->isStringUuid()) {
            return Uuid::v4()->toRfc4122();
        }

        if ($this->type->isStringUri()) {
            return '/access-management/roles';
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
        if ($this->minimumLength) {
            $specification['minLength'] = $this->minimumLength->toInt();
        }
        if ($this->maximumLength) {
            $specification['maxLength'] = $this->maximumLength->toInt();
        }
        if ($example) {
            $specification['example'] = $example;
        }
        return $specification;
    }
}