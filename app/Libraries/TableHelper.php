<?php

namespace App\Libraries;

use InvalidArgumentException;
use Michalsn\CodeIgniterHtmx\HTTP\IncomingRequest;
use Michalsn\CodeIgniterHtmx\HTTP\Response;

class TableHelper
{
    protected array $validSortColumns = [];

    public function __construct(
        protected IncomingRequest $request,
        protected Response $response,
        protected string $baseURL,
        protected array $search,
        protected int $page,
        protected int $perPage,
        protected string $sortColumn,
        protected string $sortDirection)
    {
    }

    public function setValidSortColumns(array $data): TableHelper
    {
        $this->validSortColumns = $data;

        return $this;
    }

    public function baseURL(): string
    {
        $queryString = [
            'sortColumn'    => $this->sortColumn,
            'sortDirection' => $this->sortDirection,
        ];

        return $this->baseURL . '?' . http_build_query($queryString);
    }

    public function sortByURL(string $column): string
    {
        if ($this->validSortColumns !== [] && ! in_array($column, $this->validSortColumns, true)) {
            throw new InvalidArgumentException('Sort column is out of the range.');
        }

        $queryString = [
            'sortColumn'    => $column,
            'sortDirection' => $this->sortColumn === $column && $this->sortDirection === 'asc' ? 'desc' : 'asc',
        ];

        return $this->baseURL . '?' . http_build_query($queryString);
    }

    public function getSortIndicator(string $column): string
    {
        if ($this->validSortColumns !== [] && ! in_array($column, $this->validSortColumns, true)) {
            throw new InvalidArgumentException('Sort column is out of the range.');
        }

        if ($column === $this->sortColumn) {
            return $this->sortDirection === 'asc' ? '↑' : '↓';
        }

        return '';
    }

    public function setReplaceUrlHeader(): TableHelper
    {
        if ($this->request->isBoosted()) {
            return $this;
        }

        $queryString = [
            'sortColumn'    => $this->sortColumn,
            'sortDirection' => $this->sortDirection,
            'perPage'       => $this->perPage,
            'page'          => $this->page,
        ];

        if ($this->search) {
            $queryString['search'] = [];
        }

        foreach ($this->search as $key => $value) {
            $queryString['search'][$key] = $value;
        }

        $this->response->setReplaceUrl($this->baseURL . '?' . http_build_query($queryString));

        return $this;
    }
}
