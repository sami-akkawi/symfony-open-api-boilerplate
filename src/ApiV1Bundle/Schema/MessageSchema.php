<?php declare(strict_types=1);

namespace App\ApiV1Bundle\Schema;

use App\Message\Message;
use App\Message\MessageType;
use App\OpenApiSpecification\ApiComponents\ComponentsExample\Example;
use App\OpenApiSpecification\ApiComponents\Schema\DetailedSchema;
use App\OpenApiSpecification\ApiComponents\Schema\DiscriminatorSchema;
use App\OpenApiSpecification\ApiComponents\Schema\MapSchema;
use App\OpenApiSpecification\ApiComponents\Schema\ObjectSchema;
use App\OpenApiSpecification\ApiComponents\Schema\Schema\SchemaType;
use App\OpenApiSpecification\ApiComponents\Schema\StringSchema;
use App\OpenApiSpecification\ApiComponents\Schemas;

final class MessageSchema extends AbstractSchema
{
    private ObjectSchema $schema;

    private function __construct(ObjectSchema $schema)
    {
        $this->schema = $schema;
    }

    public function toDetailedSchema(): DetailedSchema
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

    protected static function getOpenApiSchemaWithoutName(): DetailedSchema
    {
        return ObjectSchema::generate(
            Schemas::generate()
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