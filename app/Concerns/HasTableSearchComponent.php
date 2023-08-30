<?php

namespace App\Concerns;

use CodeIgniter\Pager\Pager;
use InvalidArgumentException;

trait HasTableSearchComponent
{
    protected string $baseURL;

    protected string $sortColumn;
    protected array $validSortColumns;

    protected string $sortDirection;
    protected array $validSortDirections;

    protected int $perPage;
    protected array $validPerPage;

    protected int $page;

    protected array $search;
    protected array $validSearch;

    private function parseRules(array $rules): array
    {
        $options    = null;
        $rulesArray = [];

        foreach ($rules as $key => $value) {
            if (is_array($value)) {
                $rulesArray[] = sprintf('%s[%s]', $key, implode(',', $value));

                if ($options === null) {
                    $options = $value;
                }
            } else {
                $rulesArray[] = $value;
            }
        }

        return [$rulesArray, $options];
    }

    /**
     * @throws InvalidArgumentException
     */
    private function validateRules(string $value, array $rules, string $label): ?array
    {
        [$rules, $options] = $this->parseRules($rules);

        $valid = service('validation')
            ->reset()
            ->setRule('check', $label, $rules)
            ->run(['check' => $value]);

        if (! $valid) {
            throw new InvalidArgumentException(service('validation')->getError('check'));
        }

        return $options;
    }

    protected function defineBaseURL(string $value): void
    {
        $this->baseURL = $value;
    }

    protected function defineSortColumns(string $value, array $rules): void
    {

        $options = $this->validateRules($value, $rules, 'sortColumn');

        $this->sortColumn = $value;
        $this->validSortColumns = $options;
    }

    protected function defineSortDirections(string $value, array $rules): void
    {
        $options = $this->validateRules($value, $rules, 'sortDirection');

        $this->sortDirection = $value;
        $this->validSortDirections = $options;
    }

    protected function definePerPage(int $value, array $rules): void
    {
        $options = $this->validateRules($value, $rules, 'perPage');

        $this->perPage = $value;
        $this->validPerPage = $options;
    }

    protected function definePage(int $value, array $rules): void
    {
        $this->validateRules($value, $rules, 'page');

        $this->page = $value;
    }

    protected function defineSearch(array $fields): void
    {
        foreach ($fields as $name => $options) {
            $this->parseSearchDefinition($name, $options);
        }

        $this->setReplaceUrlHeader();
    }

    protected function parseSearchDefinition(string $name, array $options): void
    {
        [$value, $rules] = $options;

        $options = $this->validateRules($value, $rules, $name);

        $this->search[$name] = $value;
        $this->validSearch[$name] = $options;
    }

    protected function setReplaceUrlHeader(): void
    {
        if (service('request')->is('boosted')) {
            return;
        }

        $queryString = [
            'sortColumn'    => $this->sortColumn,
            'sortDirection' => $this->sortDirection,
            'perPage'       => $this->perPage,
            'page'          => $this->page
        ];

        if ($this->search) {
            $queryString['search'] = [];
        }

        foreach ($this->search as $key => $value) {
            $queryString['search'][$key] = $value;
        }

        service('response')->setReplaceUrl($this->baseURL . '?' . http_build_query($queryString));;
    }

    protected function baseURL(): string
    {
        $queryString = [
            'sortColumn'    => $this->sortColumn,
            'sortDirection' => $this->sortDirection,
        ];

        return $this->baseURL . '?' . http_build_query($queryString);
    }

    protected function sortByURL(string $column): string
    {
        if (! in_array($column, $this->validSortColumns)) {
            throw new InvalidArgumentException('sortColumn is out of the range.');
        }

        $queryString = [
            'sortColumn'    => $column,
            'sortDirection' => $this->sortColumn === $column && $this->sortDirection === 'asc' ? 'desc' : 'asc',
        ];

        return $this->baseURL . '?' . http_build_query($queryString);
    }

    protected function getSortIndicator(string $column): string
    {
        if (! in_array($column, $this->validSortColumns)) {
            throw new InvalidArgumentException('sortColumn is out of the range.');
        }

        if ($column === $this->sortColumn) {
            return $this->sortDirection === 'asc' ? '↑' : '↓';
        }

        return '';
    }

    protected function getPagerProperty(): Pager
    {
        return $this->pager;
    }

    protected function getSearchProperty(): array
    {
        return $this->search;
    }
}
