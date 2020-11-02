<?php declare(strict_types=1);

namespace App\OpenApiSpecification\ApiComponents\ComponentsSchema;

use App\Message\FieldMessage;
use App\Message\Message;
use App\OpenApiSpecification\ApiComponents\ComponentsExample;
use App\OpenApiSpecification\ApiComponents\ComponentsSchema\Schema\SchemaIsDeprecated;
use App\OpenApiSpecification\ApiComponents\ComponentsSchema\Schema\SchemaIsNullable;
use App\OpenApiSpecification\ApiComponents\ComponentsSchema\Schema\SchemaItemsAreUnique;
use App\OpenApiSpecification\ApiComponents\ComponentsSchema\Schema\SchemaMaximumItems;
use App\OpenApiSpecification\ApiComponents\ComponentsSchema\Schema\SchemaMinimumItems;
use App\OpenApiSpecification\ApiComponents\ComponentsSchema;
use App\OpenApiSpecification\ApiComponents\ComponentsSchema\Schema\SchemaDescription;
use App\OpenApiSpecification\ApiComponents\ComponentsSchema\Schema\SchemaIsRequired;
use App\OpenApiSpecification\ApiComponents\ComponentsSchema\Schema\SchemaName;
use App\OpenApiSpecification\ApiComponents\ComponentsSchema\Schema\SchemaType;
use App\OpenApiSpecification\ApiException\SpecificationException;

final class ArraySchema extends Schema
{
    private ComponentsSchema $itemType;
    protected ?SchemaName $name;
    private SchemaItemsAreUnique $itemsAreUnique;
    private SchemaType $type;
    private ?SchemaDescription $description;
    private ?SchemaMinimumItems $minimumItems;
    private ?SchemaMaximumItems $maximumItems;

    private function __construct(
        ComponentsSchema $itemType,
        SchemaIsRequired $isRequired,
        ?SchemaName $name = null,
        ?SchemaItemsAreUnique $itemsAreUnique = null,
        ?SchemaDescription $description = null,
        ?SchemaIsNullable $isNullable = null,
        ?ComponentsExample $example = null,
        ?SchemaMinimumItems $minimumItems = null,
        ?SchemaMaximumItems $maximumItems = null,
        ?SchemaIsDeprecated $isDeprecated = null
    ) {
        $this->itemType = $itemType;
        $this->isRequired = $isRequired;
        $this->isDeprecated = $isDeprecated ?? SchemaIsDeprecated::generateFalse();
        $this->name = $name;
        $this->itemsAreUnique = $itemsAreUnique ?? SchemaItemsAreUnique::generateFalse();
        $this->type = SchemaType::generateArray();
        $this->description = $description;
        $this->isNullable = $isNullable ?? SchemaIsNullable::generateFalse();
        $this->example = $example;
        $this->minimumItems = $minimumItems;
        $this->maximumItems = $maximumItems;
    }

    public function getType(): SchemaType
    {
        return $this->type;
    }

    public function getItemType(): ComponentsSchema
    {
        return $this->itemType;
    }

    public function setName(string $name): self
    {
        return new self(
            $this->itemType,
            $this->isRequired,
            SchemaName::fromString($name),
            $this->itemsAreUnique,
            $this->description,
            $this->isNullable,
            $this->example,
            $this->minimumItems,
            $this->maximumItems,
            $this->isDeprecated
        );
    }

    public function makeValuesUnique(): self
    {
        return new self(
            $this->itemType,
            $this->isRequired,
            $this->name,
            SchemaItemsAreUnique::generateTrue(),
            $this->description,
            $this->isNullable,
            $this->example,
            $this->minimumItems,
            $this->maximumItems,
            $this->isDeprecated
        );
    }

    public static function generate(ComponentsSchema $itemType): self
    {
        return new self($itemType, SchemaIsRequired::generateFalse());
    }

    public function isValueValid($value): array
    {
        $errors = [];
        if ($this->isNullable->toBool() && is_null($value)) {
            return $errors;
        }
        if (!is_array($value)) {
            $errorMessage = $this->getWrongTypeMessage('array', $value);
            $errors[] = $this->name ?
                FieldMessage::generate([$this->name->toString()], $errorMessage) :
                $errorMessage;
            return $errors;
        }
        $lengthErrorMessage = $this->isArrayLengthValid($value);
        if ($lengthErrorMessage) {
            $errors[] = $this->name ?
                FieldMessage::generate([$this->name->toString()], $lengthErrorMessage) :
                $lengthErrorMessage;
        }
        foreach ($value as $item) {
            $subErrors = $this->itemType->isValueValid($item);
            if ($subErrors) {
                $errors[] = $subErrors;
            }
        }
        return $errors;
    }

    public function setDescription(string $description): self
    {
        return new self(
            $this->itemType,
            $this->isRequired,
            $this->name,
            $this->itemsAreUnique,
            SchemaDescription::fromString($description),
            $this->isNullable,
            $this->example,
            $this->minimumItems,
            $this->maximumItems,
            $this->isDeprecated
        );
    }

    public function deprecate(): self
    {
        return new self(
            $this->itemType,
            $this->isRequired,
            $this->name,
            $this->itemsAreUnique,
            $this->description,
            $this->isNullable,
            $this->example,
            $this->minimumItems,
            $this->maximumItems,
            SchemaIsDeprecated::generateTrue()
        );
    }

    public function setExample(ComponentsExample $example): self
    {
        $exception = $this->validateValue($example->toExample()->getLiteralValue());
        if ($exception) {
            throw $exception;
        }

        return new self(
            $this->itemType,
            $this->isRequired,
            $this->name,
            $this->itemsAreUnique,
            $this->description,
            $this->isNullable,
            $example,
            $this->minimumItems,
            $this->maximumItems,
            $this->isDeprecated
        );
    }

    public function makeNullable(): self
    {
        return new self(
            $this->itemType,
            $this->isRequired,
            $this->name,
            $this->itemsAreUnique,
            $this->description,
            SchemaIsNullable::generateTrue(),
            $this->example,
            $this->minimumItems,
            $this->maximumItems,
            $this->isDeprecated
        );
    }

    public function require(): self
    {
        return new self(
            $this->itemType,
            SchemaIsRequired::generateTrue(),
            $this->name,
            $this->itemsAreUnique,
            $this->description,
            $this->isNullable,
            $this->example,
            $this->minimumItems,
            $this->maximumItems,
            $this->isDeprecated
        );
    }

    public function unRequire(): self
    {
        return new self(
            $this->itemType,
            SchemaIsRequired::generateFalse(),
            $this->name,
            $this->itemsAreUnique,
            $this->description,
            $this->isNullable,
            $this->example,
            $this->minimumItems,
            $this->maximumItems,
            $this->isDeprecated
        );
    }

    private function areLengthSettingsValid(
        ?SchemaMinimumItems $minimumItems,
        ?SchemaMaximumItems $maximumItems
    ): bool
    {
        if (!$minimumItems || !$maximumItems) {
            return true;
        }

        return $minimumItems->toInt() <= $maximumItems->toInt();
    }

    public function setMinimumItems(int $minItems): self
    {
        if ($minItems < 0) {
            throw SpecificationException::generateMinimumItemsCannotBeLessThanZero();
        }

        $minimumItems = SchemaMinimumItems::fromInt($minItems);
        if (!$this->areLengthSettingsValid($minimumItems, $this->maximumItems)) {
            throw SpecificationException::generateMinimumShouldBeLessThanMaximum();
        }

        return new self(
            $this->itemType,
            $this->isRequired,
            $this->name,
            $this->itemsAreUnique,
            $this->description,
            $this->isNullable,
            $this->example,
            $minimumItems,
            $this->maximumItems,
            $this->isDeprecated
        );
    }

    public function setMaximumItems(int $maxItems): self
    {
        $maximumItems = SchemaMaximumItems::fromInt($maxItems);
        if (!$this->areLengthSettingsValid($this->minimumItems, $maximumItems)) {
            throw SpecificationException::generateMinimumShouldBeLessThanMaximum();
        }

        return new self(
            $this->itemType,
            $this->isRequired,
            $this->name,
            $this->itemsAreUnique,
            $this->description,
            $this->isNullable,
            $this->example,
            $this->minimumItems,
            $maximumItems,
            $this->isDeprecated
        );
    }

    public function getMaximumItems(): ?SchemaMaximumItems
    {
        return $this->maximumItems;
    }

    public function getMinimumItems(): ?SchemaMinimumItems
    {
        return $this->minimumItems;
    }

    public function isArrayLengthValid(array $array): ?Message
    {
        $arrayLength = count($array);
        if ($this->minimumItems && $arrayLength < $this->minimumItems->toInt()) {
            return Message::generateError(
                'less_than_minimum',
                "Array has $arrayLength items, minimum allowed is: " . $this->minimumItems->toInt(),
                [
                    '%correctMinimum%' => (string)$this->minimumItems->toInt(),
                    '%suppliedMinimum%' => (string)$arrayLength
                ]
            );
        }

        if ($this->maximumItems && $arrayLength > $this->maximumItems->toInt()) {
            return Message::generateError(
                'more_than_maximum',
                "Array has $arrayLength items, maximum allowed is: " . $this->maximumItems->toInt(),
                [
                    '%correctMaximum%' => (string)$this->minimumItems->toInt(),
                    '%suppliedMaximum%' => (string)$arrayLength
                ]
            );
        }

        return null;
    }

    public function toOpenApiSpecification(): array
    {
        $specification = [
            'type' => $this->type->getType(),
            'items' => $this->itemType->toOpenApiSpecification()
        ];
        if ($this->itemsAreUnique) {
            $specification['uniqueItems'] = true;
        }
        if ($this->minimumItems) {
            $specification['minItems'] = $this->minimumItems->toInt();
        }
        if ($this->maximumItems) {
            $specification['maxItems'] = $this->maximumItems->toInt();
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
        if ($this->example) {
            $specification['example'] = $this->example->getLiteralValue();
        }
        return $specification;
    }

    public function getValueFromTrimmedCastedString(string $value): array
    {
        $castedArray = [];
        $array = json_decode($value, true);

        if ($array === null) {
            $array = explode(',', $value);
        }

        foreach ($array as $entry) {
            $castedArray[] = $this->itemType->getValueFromCastedString(json_encode($entry));
        }

        return $castedArray;
    }
}