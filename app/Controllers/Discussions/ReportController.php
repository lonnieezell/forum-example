<?php

namespace App\Controllers\Discussions;

use App\Controllers\BaseController;
use App\Entities\ModerationReport;
use App\Models\ModerationReportModel;
use Config\Forum;
use ReflectionException;

/**
 * Class Report
 */
class ReportController extends BaseController
{
    /**
     * Display modal.
     */
    public function index(int $id, string $resourceType)
    {
        helper(['form', 'inflector']);

        $type = plural($resourceType);

        if (! $this->policy->can($type . '.report')) {
            return alerts()->set('error', 'You do not have permission to report ' . $type);
        }

        if ($this->request->is('post')) {
            return $this->insert($id, $resourceType);
        }

        return view('discussions/report/modal', ['type' => $resourceType]);
    }

    /**
     * Add report.
     *
     * @throws ReflectionException
     */
    protected function insert(int $id, string $resourceType)
    {
        $data = [
            'resource_id'   => $id,
            'resource_type' => $resourceType,
            'comment'       => $this->request->getPost('comment'),
        ];

        $userId = user_id();
        $rule   = $resourceType === 'thread' ? "thread_report[{$userId}]" : "post_report[{$userId}]";

        $rules = [
            'resource_id'   => ['required', $rule, "unique_report[{$resourceType},{$userId}]"],
            'resource_type' => ['required', 'in_list[thread,post]'],
            'comment'       => ['required', 'min_length[5]', 'max_length[255]'],
        ];

        if (! $this->validateData($data, $rules)) {
            foreach ($this->validator->getErrors() as $error) {
                alerts()->set('error', $error);
            }

            return view('discussions/report/modal_content', ['type' => $resourceType]);
        }

        // Throttle number of reports per user
        if (service('throttler')->check(md5('report-' . $userId), config(Forum::class)->maxReportsPerDey, DAY) === false) {
            alerts()->set('error', 'You have reached the maximum number of reports for today.');
            $this->response->triggerClientEvent('closeModal');

            return '';
        }

        $report = new ModerationReport($this->validator->getValidated());
        $report->fill(['author_id' => $userId]);

        if (model(ModerationReportModel::class)->insert($report)) {
            alerts()->set('success', 'Your report has been submitted. Thank you.');
            // Hide modal
            $this->response->triggerClientEvent('closeModal');
        } else {
            alerts()->set('error', 'Something went wrong');
        }

        return '';
    }
}
