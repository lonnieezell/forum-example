<?php

namespace App\Controllers\Members;

use App\Controllers\BaseController;
use App\Libraries\TableHelper;
use App\Models\UserModel;
use InvalidArgumentException;

/**
 * Class MemberController
 */
class MemberController extends BaseController
{
    /**
     * Display a standard forum-style list of users.
     */
    public function list()
    {
        $table = [
            'perPage'       => $this->request->getGet('perPage') ?? 5,
            'page'          => $this->request->getGet('page') ?? 1,
            'search'        => $this->request->getGet('search') ?? [],
            'sortColumn'    => $this->request->getGet('sortColumn') ?? 'username',
            'sortDirection' => $this->request->getGet('sortDirection') ?? 'asc',
        ];

        $roleKeys = array_merge(['all'], array_keys(setting('AuthGroups.groups')));

        $rules = [
            'perPage'         => ['in_list[5]'],
            'page'            => ['is_natural', 'greater_than_equal_to[1]'],
            'search.username' => ['permit_empty', 'string', 'max_length[20]'],
            'search.country'  => ['permit_empty', 'string', 'max_length[20]'],
            'search.role'     => ['permit_empty', 'in_list[' . implode(',', $roleKeys) . ']'],
            'search.type'     => ['permit_empty', 'in_list[all,new]'],
            'sortColumn'      => ['in_list[username,role,count,country,last_active]'],
            'sortDirection'   => ['in_list[asc,desc]'],
        ];

        if (! $this->validateData($table, $rules)) {
            throw new InvalidArgumentException(implode(PHP_EOL, $this->validator->getErrors()));
        }

        helper('form');

        $userModel = model(UserModel::class);

        $data = [
            'members' => $userModel->searchMembers(
                $table['search'],
                $table['page'],
                $table['perPage'],
                $table['sortColumn'],
                $table['sortDirection']
            ),
        ];

        $table['dropdowns'] = [
            'role' => array_combine($roleKeys, array_merge(['All'], array_column(setting('AuthGroups.groups'), 'title'))),
            'type' => ['all' => 'All users', 'new' => 'New users'],
        ];

        $table['pager']  = $userModel->pager;
        $table['helper'] = new TableHelper($this->request, $this->response, 'members', $table['search'], $table['page'], $table['perPage'], $table['sortColumn'], $table['sortDirection']);
        $table['helper']->setReplaceUrlHeader();

        $data['table'] = $table;

        return $this->render('members/list', $data);
    }
}
