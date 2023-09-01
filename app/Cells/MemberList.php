<?php

namespace App\Cells;

use App\Concerns\HasTableSearchComponent;
use App\Models\UserModel;
use CodeIgniter\Pager\Pager;
use CodeIgniter\View\Cells\Cell;

class MemberList extends Cell
{
    use HasTableSearchComponent;

    protected string $view = 'member_list_cell';

    protected array $members;
    protected Pager $pager;

    public function mount()
    {
        $this->defineTableSearchComponent();

        helper('form');

        $model = model(UserModel::class);

        $this->members = $model->searchMembers(
            $this->search, $this->page, $this->perPage, $this->sortColumn, $this->sortDirection
        );

        $this->pager = $model->pager->setPath($this->baseURL);
    }

    protected function getMembersProperty(): array
    {
        return $this->members;
    }

    protected function validRoles(): array
    {
        return array_combine($this->validSearch['role'], $this->labelSearch['role']);
    }

    protected function validTypes(): array
    {
        return array_combine($this->validSearch['type'], $this->labelSearch['type']);
    }

    private function defineTableSearchComponent(): void
    {
        $this->defineBaseURL('members');

        $this->defineSortColumns(
            service('request')->getGet('sortColumn') ?? 'username',
            ['in_list' => ['username', 'role', 'count', 'country', 'last_active']]
        );

        $this->defineSortDirections(
            service('request')->getGet('sortDirection') ?? 'asc',
            ['in_list' => ['asc', 'desc']]
        );

        $this->definePerPage(
            service('request')->getGet('perPage') ?? 5,
            ['in_list' => [5, 10, 20]]
        );

        $this->definePage(
            service('request')->getGet('page') ?? 1,
            ['is_natural_no_zero']
        );

        $search = service('request')->getGet('search');
        $this->defineSearch([
            'user' => [
                $search['user'] ?? '',
                ['string', 'max_length[10]']
            ],
            'country' => [
                $search['country'] ?? '',
                ['string', 'max_length[10]']
            ],
            'role' => [
                $search['role'] ?? 'all',
                ['in_list' => array_merge(['all'], array_keys(setting('AuthGroups.groups')))],
                array_merge(['All'], array_column(setting('AuthGroups.groups'), 'title'))

            ],
            'type' => [
                $search['type'] ?? 'all',
                ['in_list' => ['all', 'new']],
                ['All users', 'New users']
            ],
        ]);
    }
}
