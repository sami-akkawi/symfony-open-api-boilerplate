<?php declare(strict_types=1);

namespace App\ApiV1Bundle\Schema;

use App\OpenApiSpecification\ApiComponents\ComponentsSchema\Schema;
use App\OpenApiSpecification\ApiComponents\ComponentsSchema\IntegerSchema;
use App\OpenApiSpecification\ApiComponents\ComponentsSchema\ObjectSchema;
use App\OpenApiSpecification\ApiComponents\ComponentsSchema\StringSchema;
use App\OpenApiSpecification\ApiComponents\ComponentsSchemas;

final class PaginationSchema extends AbstractSchema
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
                ->addSchema(IntegerSchema::generate()
                    ->setName('totalCount')
                    ->setDescription('Total number of results.'))
                ->addSchema(IntegerSchema::generate()
                    ->setName('filteredCount')
                    ->setDescription('Total number of results after filter was applied.'))
                ->addSchema(IntegerSchema::generate()
                    ->setName('perPage')
                    ->setDescription('Number of results shown per page. 0 will return all results in one page.')
                    ->setMinimum(0))
                ->addSchema(IntegerSchema::generate()
                    ->setName('currentPage')
                    ->setDescription('Index of the current page.'))
                ->addSchema(IntegerSchema::generate()
                    ->setName('lastPage')
                    ->setDescription('Index of the last page.'))
                ->addSchema(ObjectSchema::generate(
                    ComponentsSchemas::generate()
                        ->addSchema(StringSchema::generate()
                            ->setName('first')
                            ->setDescription('Link to the endpoint of the first page.'))
                        ->addSchema(StringSchema::generate()
                            ->setName('previous')
                            ->setDescription('Link to the endpoint of the previous page.'))
                        ->addSchema(StringSchema::generate()
                            ->setName('current')
                            ->setDescription('Link to the endpoint of the current page.'))
                        ->addSchema(StringSchema::generate()
                            ->setName('next')
                            ->setDescription('Link to the endpoint of the next page.'))
                        ->addSchema(StringSchema::generate()
                            ->setName('last')
                            ->setDescription('Link to the endpoint of the last page.'))
                    )->setName('links')
                )
        );
    }
}