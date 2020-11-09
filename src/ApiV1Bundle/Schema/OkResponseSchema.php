<?php declare(strict_types=1);

namespace App\ApiV1Bundle\Schema;

use App\OpenApiSpecification\ApiComponents\ComponentsSchema;
use App\OpenApiSpecification\ApiComponents\ComponentsSchema\ArraySchema;
use App\OpenApiSpecification\ApiComponents\ComponentsSchema\DiscriminatorSchema;
use App\OpenApiSpecification\ApiComponents\ComponentsSchema\ObjectSchema;
use App\OpenApiSpecification\ApiComponents\ComponentsSchema\Schema;
use App\OpenApiSpecification\ApiComponents\ComponentsSchemas;

final class OkResponseSchema extends AbstractSchema
{
    private ObjectSchema $schema;

    private function __construct(ObjectSchema $schema)
    {
        $this->schema = $schema;
    }

    public function toDetailedSchema(): Schema
    {
        return $this->schema;
    }

    public static function getCustom(
        ComponentsSchema $data,
        ?string $description = null,
        bool $withPagination = false,
        bool $withMessages = false,
        bool $withFieldMessages = false
    ): ObjectSchema {
        return self::getSchema($data, $description, $withPagination, $withMessages, $withFieldMessages);
    }

    public static function getWithoutDataWithOnlyMessages(): ObjectSchema
    {
        return self::getSchema(null, null, false, true, false);
    }

    private static function getSchema(
        ?ComponentsSchema $data,
        ?string $description,
        bool $withPagination,
        bool $withMessages,
        bool $withFieldMessages,
        bool $makeAllNullable = false
    ): ObjectSchema {
        $responseItems = ComponentsSchemas::generate();
        if ($data) {
            $responseItems = $responseItems->addDataSchema($data);
        }
        if ($withPagination) {
            $paginationSchema = PaginationSchema::getReferenceSchema();
            if ($makeAllNullable) {
                $paginationSchema = $paginationSchema->makeNullable();
            }
            $paginationSchema = $paginationSchema->setName('pagination');
            $responseItems = $responseItems->addSchema($paginationSchema);
        }
        if ($withMessages) {
            $messagesSchema = MessagesSchema::getReferenceSchema();
            if ($makeAllNullable) {
                $messagesSchema = $messagesSchema->makeNullable();
            }
            $messagesSchema = $messagesSchema->setName('generalMessages');
            $responseItems = $responseItems->addSchema($messagesSchema);
        }
        if ($withFieldMessages) {
            $fieldMessagesSchema = FieldMessagesSchema::getReferenceSchema();
            if ($makeAllNullable) {
                $fieldMessagesSchema = $fieldMessagesSchema->makeNullable();
            }
            $fieldMessagesSchema = $fieldMessagesSchema->setName('fieldMessages');
            $responseItems = $responseItems->addSchema($fieldMessagesSchema);
        }

        $responseSchema = ObjectSchema::generate($responseItems);
        if ($description) {
            $responseSchema = $responseSchema->setDescription($description);
        }

        return $responseSchema;
    }

    protected static function getOpenApiSchemaWithoutName(): Schema
    {
        return self::getSchema(
            DiscriminatorSchema::generateOneOf()
                ->addSchema(ObjectSchema::generateEmpty()->setDescription('An object.'))
                ->addSchema(ArraySchema::generate(ObjectSchema::generateEmpty())->setDescription('An array of the same object.'))
                ->setDescription('The data of the response.'),
            'This is a general example of an Ok response.',
            true,
            true,
            true,
            true
        );
    }

    public static function getAlwaysRequiredFields(): array
    {
        return [];
    }

    public function requireOnly(array $fieldNames)
    {
        $requireAlways = self::getAlwaysRequiredFields();
        $newSchema = $this->schema->requireOnly(array_merge($requireAlways , $fieldNames));
        return new self($newSchema);
    }
}