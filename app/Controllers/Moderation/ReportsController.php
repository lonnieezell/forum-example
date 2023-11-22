<?php

namespace App\Controllers\Moderation;

use App\Concerns\HasAuthorsAndEditors;
use App\Concerns\HasThreadsAndPosts;
use App\Controllers\BaseController;
use App\Enums\ModerationLogStatus;
use App\Libraries\TableHelper;
use App\Models\ModerationIgnoredModel;
use App\Models\ModerationLogModel;
use App\Models\ModerationReportModel;
use App\Models\UserModel;
use InvalidArgumentException;
use ReflectionException;

class ReportsController extends BaseController
{
    use HasAuthorsAndEditors;
    use HasThreadsAndPosts;

    /**
     * Show moderation queue.
     */
    public function list(string $resourceType = 'thread')
    {
        $table = [
            'resourceType'  => $resourceType,
            'perPage'       => $this->request->getGet('perPage') ?? 10,
            'page'          => $this->request->getGet('page') ?? 1,
            'sortColumn'    => $this->request->getGet('sortColumn') ?? 'created_at',
            'sortDirection' => $this->request->getGet('sortDirection') ?? 'asc',
        ];

        $rules = [
            'resourceType'    => ['in_list[thread,post]'],
            'perPage'         => ['in_list[10]'],
            'page'            => ['is_natural', 'greater_than_equal_to[1]'],
            'sortColumn'      => ['in_list[created_at]'],
            'sortDirection'   => ['in_list[asc,desc]'],
        ];

        if (! $this->validateData($table, $rules)) {
            throw new InvalidArgumentException(implode(PHP_EOL, $this->validator->getErrors()));
        }

        helper(['form', 'inflector', 'text']);

        $reportModel = model(ModerationReportModel::class);

        $data = [
            'reports' => $reportModel->list(
                user_id(),
                $table['resourceType'],
                $table['page'],
                $table['perPage'],
                $table['sortColumn'],
                $table['sortDirection']
            ),
        ];

        $table['pager']  = $reportModel->pager;
        $table['helper'] = new TableHelper(
            $this->request,
            $this->response,
            route_to('moderate-' . plural($resourceType)),
            [],
            $table['page'],
            $table['perPage'],
            $table['sortColumn'],
            $table['sortDirection']
        );
        $table['helper']->setReplaceUrlHeader();

        $data['table'] = $table;

        return $this->render('moderation/reports/list', $data);
    }

    /**
     * Make an action on the reported item.
     *
     * @throws ReflectionException
     */
    public function action(string $resourceType)
    {
        $data = [
            'resourceType'  => $resourceType,
            'action'        => $this->request->getPost('action'),
            'items'         => $this->request->getPost('items'),
        ];

        $rules = [
            'resourceType' => ['required', 'in_list[thread,post]'],
            'action'       => ['required', 'in_list[approve,deny,ignore]'],
            'items.*'      => ['required', 'is_natural'],
        ];

        if (! $this->validateData($data, $rules)) {
            foreach ($this->validator->getErrors() as $error) {
                alerts()->set('error', $error);
            }
            return '';
        }

        $userId = user_id();

        match($data['action']) {
            'approve' => model(ModerationReportModel::class)->action($resourceType, ModerationLogStatus::APPROVED, $data['items'], $userId),
            'deny'   => model(ModerationReportModel::class)->action($resourceType, ModerationLogStatus::DENIED, $data['items'], $userId),
            'ignore' => model(ModerationIgnoredModel::class)->ignore($data['items'], $userId)
        };

        $this->response->setRetarget('body');

        return $this->list($resourceType);
    }

    /**
     * Show moderation logs.
     */
    public function logs()
    {
        $table = [
            'perPage'       => $this->request->getGet('perPage') ?? 20,
            'page'          => $this->request->getGet('page') ?? 1,
            'search'        => $this->request->getGet('search') ?? [],
            'sortColumn'    => $this->request->getGet('sortColumn') ?? 'created_at',
            'sortDirection' => $this->request->getGet('sortDirection') ?? 'desc',
        ];

        // Format available log statuses
        $logStatuses = array_column(ModerationLogStatus::cases(), 'value');
        $dates       = ['today' => 'Today', 'yesterday' => 'Yesterday', 'last7Days' => 'Last 7 days', 'last30Days' => 'Last 30 days', 'thisYear' => 'This year', 'custom' => 'Custom date range'];

        $rules = [
            'perPage'               => ['in_list[20]'],
            'page'                  => ['is_natural', 'greater_than_equal_to[1]'],
            'search.resourceType'   => ['permit_empty', 'in_list[thread,post]'],
            'search.status'         => ['permit_empty', 'in_list['.implode(',', $logStatuses).']'],
            'search.authorId'       => ['permit_empty', 'is_natural', 'greater_than_equal_to[1]'],
            'search.createdAt'      => ['permit_empty', 'in_list['.implode(',', array_keys($dates)).']'],
            'search.createdAtRange' => ['permit_empty', 'date_range_when_field[search.createdAt,custom]'],
            'sortColumn'            => ['in_list[resource_type,status,created_at]'],
            'sortDirection'         => ['in_list[asc,desc]'],
        ];

        if (! $this->validateData($table, $rules)) {
            throw new InvalidArgumentException(implode(PHP_EOL, $this->validator->getErrors()));
        }

        helper(['form', 'inflector', 'text']);

        $logModel = model(ModerationLogModel::class);

        $data = [
            'logs' => $logModel->list(
                $table['search'],
                $table['page'],
                $table['perPage'],
                $table['sortColumn'],
                $table['sortDirection']
            ),
        ];

        $data['logs'] = $this->withThreadsAndPosts($data['logs']);
        $data['logs'] = $this->withUsers($data['logs']);

        if ($authorIds = $logModel->getAuthorIds()) {
            $authorIds = model(UserModel::class)->dropdown('id', 'username', $authorIds);
        }

        $table['dropdowns'] = [
            'resourceType' => ['' => 'All types', 'thread' => 'Threads', 'post' => 'Posts'],
            'status' => array_merge(['' => 'All statuses'], [...array_combine($logStatuses, array_map(fn($status) => ucfirst($status), $logStatuses))]),
            'authorId' => array_replace_recursive(['' => 'All moderators'], $authorIds),
            'createdAt' => array_merge(['' => 'All time'], $dates),
        ];

        $table['pager']  = $logModel->pager;
        $table['helper'] = new TableHelper($this->request, $this->response, route_to('moderate-logs'), $table['search'], $table['page'], $table['perPage'], $table['sortColumn'], $table['sortDirection']);
        $table['helper']->setReplaceUrlHeader();

        $data['table'] = $table;

        return $this->render('moderation/reports/logs', $data);
    }
}
