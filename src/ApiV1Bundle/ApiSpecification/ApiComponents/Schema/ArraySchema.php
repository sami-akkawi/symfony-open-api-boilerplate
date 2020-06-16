<?php declare(strict=1);

namespace App\ApiV1Bundle\ApiSpecification\ApiComponents\Schema;

use App\ApiV1Bundle\ApiSpecification\ApiComponents\Example;
use App\ApiV1Bundle\ApiSpecification\ApiComponents\Schema\Schema\SchemaIsNullable;
use App\ApiV1Bundle\ApiSpecification\ApiComponents\Schema\Schema\SchemaItemsAreUnique;
use App\ApiV1Bundle\ApiSpecification\ApiComponents\Schema\Schema\SchemaMaximumItems;
use App\ApiV1Bundle\ApiSpecification\ApiComponents\Schema\Schema\SchemaMinimumItems;
use App\ApiV1Bundle\ApiSpecification\ApiComponents\Schema;
use App\ApiV1Bundle\ApiSpecification\ApiComponents\Schema\Schema\SchemaDescription;
use App\ApiV1Bundle\ApiSpecification\ApiComponents\Schema\Schema\SchemaIsRequired;
use App\ApiV1Bundle\ApiSpecification\ApiComponents\Schema\Schema\SchemaName;
use App\ApiV1Bundle\ApiSpecification\ApiComponents\Schema\Schema\SchemaType;
use App\ApiV1Bundle\ApiSpecification\ApiException\SpecificationException;

final class ArraySchema extends DetailedSchema
{
    private Schema $itemType;
    protected ?SchemaName $name;
    private SchemaItemsAreUnique $itemsAreUnique;
    private SchemaType $type;
    private ?SchemaDescription $description;
    private ?SchemaMinimumItems $minimumItems;
    private ?SchemaMaximumItems $maximumItems;

    private function __construct(
        Schema $itemType,
        SchemaIsRequired $isRequired,
        ?SchemaName $name = null,
        ?SchemaItemsAreUnique $itemsAreUnique = null,
        ?SchemaDescription $description = null,
        ?SchemaIsNullable $isNullable = null,
        ?Example $example = null,
        ?SchemaMinimumItems $minimumItems = null,
        ?SchemaMaximumItems $maximumItems = null
    ) {
        $this->itemType = $itemType;
        $this->isRequired = $isRequired;
        $this->name = $name;
        $this->itemsAreUnique = $itemsAreUnique ?? SchemaItemsAreUnique::generateFalse();
        $this->type = SchemaType::generateArray();
        $this->description = $description;
        $this->isNullable = $isNullable ?? SchemaIsNullable::generateFalse();
        $this->example = $example;
        $this->minimumItems = $minimumItems;
        $this->maximumItems = $maximumItems;
    }

    public function getItemType(): Schema
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
            $this->maximumItems
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
            $this->maximumItems
        );
    }

    public static function generate(Schema $itemType): self
    {
        return new self($itemType, SchemaIsRequired::generateFalse());
    }

    public function isArrayLengthValid(array $array): ?string
    {
        $arrayLength = count($array);
        if ($this->minimumItems && $arrayLength < $this->minimumItems->toInt()) {
            return "Array has $arrayLength items, minimum allowed is: " . $this->minimumItems->toInt();
        }

        if ($this->maximumItems && $arrayLength > $this->maximumItems->toInt()) {
            return "Array has $arrayLength items, maximum allowed is: " . $this->minimumItems->toInt();
        }

        return null;
    }

    public function isValueValid($value): array
    {
        $errors = [];
        if (!is_array($value)) {
            $errors[] = $this->getWrongTypeMessage('array', $value);
            return $errors;
        }
        $lengthError = $this->isArrayLengthValid($value);
        if ($lengthError) {
            $errors[] = $lengthError;
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
            $this->maximumItems
        );
    }

    public function setExample(Example $example): self
    {
        $exception = $this->validateValue($example->toDetailedExample()->toMixed());
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
            $this->maximumItems
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
            $this->maximumItems
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
            $this->maximumItems
        );
    }

    private function areLengthSettingsValid(
        ?SchemaMinimumItems $minimumItems,
        ?SchemaMaximumItems $maximumItems
    ): bool {
        if (!$minimumItems || !$maximumItems) {
            return true;
        }

        if ($minimumItems->toInt() > $maximumItems->toInt()) {
            return false;
        }

        return true;
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
            $this->maximumItems
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
            $maximumItems
        );
    }

    public function toOpenApiSpecification(): array
    {
        $specification = [
            'type' => $this->type->getType(),
            'items' => $this->itemType->toOpenApiSpecification()
        ];
        if ($this->itemsAreUnique->toBool()) {
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
        if ($this->example) {
            $specification['example'] = $this->example->toMixed();
        }
        return $specification;
    }
}