<?php declare(strict=1);

namespace App\ApiV1Bundle\Schema;

use App\ApiV1Bundle\ApiSpecification\ApiComponents\Schema\DetailedSchema;
use App\ApiV1Bundle\ApiSpecification\ApiComponents\Schema\IntegerSchema;
use App\ApiV1Bundle\ApiSpecification\ApiComponents\Schema\ObjectSchema;
use App\ApiV1Bundle\ApiSpecification\ApiComponents\Schema\StringSchema;
use App\ApiV1Bundle\ApiSpecification\ApiComponents\Schemas;

final class Pagination extends AbstractSchema
{
    private string $uri;
    private int $total;
    private int $filtered;
    private ?int $perPage;
    private int $currentPage;

    public function __construct(
        string $uri = '',
        int $total = 0,
        int $filtered = 0,
        ?int $perPage = 10,
        int $currentPage = 1
    ) {
        $this->uri = $uri;
        $this->total = $total;
        $this->filtered = $filtered;
        $this->perPage = $perPage;
        $this->currentPage = $currentPage;
    }

    public function toArray(): array
    {
        return [
            'totalCount' => $this->total,
            'filteredCount' => $this->filtered,
            'perPage' => $this->getPerPageRequestParameter(),
            'currentPage' => $this->currentPage,
            'lastPage' => $this->getLastPage(),
            'links' => $this->getLinks(),
        ];
    }

    private function getFirstPage(): int
    {
        return 1;
    }

    private function getLastPage(): int
    {
        if ($this->perPage === 0) {
            return 1;
        }
        $lastPage = (int)ceil($this->filtered / $this->perPage);
        if ($lastPage === 0) {
            return 1;
        }
        return $lastPage;
    }

    private function getPreviousPage(): int
    {
        return $this->currentPage - 1;
    }

    private function getNextPage(): int
    {
        return $this->currentPage + 1;
    }

    private function getLinks(): array
    {
        $links = [];

        if ($this->currentPage > $this->getFirstPage()) {
            $links['first'] = $this->createLink($this->getFirstPage());
            $links['previous'] = $this->createLink($this->getPreviousPage());
        }

        $links['current'] = $this->createLink($this->currentPage);

        if ($this->currentPage < $this->getLastPage()) {
            $links['next'] = $this->createLink($this->getNextPage());
            $links['last'] = $this->createLink($this->getLastPage());
        }

        return $links;
    }

    private function getPerPageRequestParameter(): ?int
    {
        return $this->perPage;
    }

    private function createLink(int $currentPage): string
    {
        $uriParts = explode('?', $this->uri);
        $queryString = $uriParts[1] ?? '';
        parse_str($queryString, $parameters);
        $parameters['perPage'] = $this->getPerPageRequestParameter();
        if ($this->perPage !== null) {
            $parameters['currentPage'] = $currentPage;
        }
        return $uriParts[0] . '?' . http_build_query($parameters);
    }

    protected static function getOpenApiSchemaWithoutName(): DetailedSchema
    {
        return ObjectSchema::generate(
            Schemas::generate()
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
                    Schemas::generate()
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