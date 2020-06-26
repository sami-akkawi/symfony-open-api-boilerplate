<?php declare(strict_types=1);

namespace App\ApiV1Bundle\Schema;

use App\Message\Message;
use App\Message\MessageType;
use App\OpenApiSpecification\ApiComponents\ComponentsExample\Example;
use App\OpenApiSpecification\ApiComponents\ComponentsSchema\Schema;
use App\OpenApiSpecification\ApiComponents\ComponentsSchema\DiscriminatorSchema;
use App\OpenApiSpecification\ApiComponents\ComponentsSchema\MapSchema;
use App\OpenApiSpecification\ApiComponents\ComponentsSchema\ObjectSchema;
use App\OpenApiSpecification\ApiComponents\ComponentsSchema\Schema\SchemaType;
use App\OpenApiSpecification\ApiComponents\ComponentsSchema\StringSchema;
use App\OpenApiSpecification\ApiComponents\ComponentsSchemas;

final class MessageSchema extends AbstractSchema
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

    public static function getAlwaysRequiredFields(): array
    {
        return [];
    }

    public function requireOnly(array $fieldNames): self
    {
        $requireAlways = self::getAlwaysRequiredFields();
        $newSchema = $this->schema->requireOnly(array_merge($requireAlways , $fieldNames));
        return new self($newSchema);
    }

    protected static function getOpenApiSchemaWithoutName(): Schema
    {
        return ObjectSchema::generate(
            ComponentsSchemas::generate()
                ->addSchema(StringSchema::generateUuid()
                    ->setName(Message::ID))
                ->addSchema(StringSchema::generate()
                    ->setName(Message::TYPE)
                    ->setOptions(MessageType::getValidTypes()))
                ->addSchema(StringSchema::generate()
                    ->setName(Message::TRANSLATION_KEY))
                ->addSchema(StringSchema::generate()
                    ->setName(Message::DEFAULT_TEXT))
                ->addSchema(MapSchema::generateStringMap()
                    ->setName(Message::PLACEHOLDERS)
                    ->makeNullable()
                    ->setExample(Example::generate(['%placeholder%' => 'myTranslation']))
                )
        );
    }
}