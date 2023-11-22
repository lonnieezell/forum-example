<?= $this->extend('master')  ?>

<?= $this->section('sidebar')  ?>
<?= view('moderation/reports/_sidebar'); ?>
<?= $this->endSection() ?>

<?= $this->section('main')  ?>

<h1>Moderation logs</h1>

<div id="report-logs">

    <div class="mt-4 my-6 flex justify-between">
        <div class="flex">
            <form action="<?= current_url(); ?>" id="logs-search" class="flex w-full" hx-target="body">
                <div class="form-control">
                    <?= form_dropdown('search[resourceType]', $table['dropdowns']['resourceType'], set_value('resourceType', $table['search']['resourceType'] ?? ''), [
                        'class' => 'select w-auto', 'hx-get' => site_url($table['helper']->baseURL()), 'hx-include' => '#logs-search'
                    ]); ?>
                </div>
                <div class="form-control">
                    <?= form_dropdown('search[status]', $table['dropdowns']['status'], set_value('status', $table['search']['status'] ?? ''), [
                        'class' => 'select w-auto', 'hx-get' => site_url($table['helper']->baseURL()), 'hx-include' => '#logs-search'
                    ]); ?>
                </div>
                <div class="form-control">
                    <?= form_dropdown('search[authorId]', $table['dropdowns']['authorId'], set_value('authorId', $table['search']['authorId'] ?? ''), [
                        'class' => 'select w-auto', 'hx-get' => site_url($table['helper']->baseURL()), 'hx-include' => '#logs-search',
                    ]); ?>
                </div>
                <div class="form-control">
                    <?= form_dropdown('search[createdAt]', $table['dropdowns']['createdAt'], set_value('createdAt', $table['search']['createdAt'] ?? ''), [
                        'class' => 'select w-auto', 'hx-get' => site_url($table['helper']->baseURL()), 'hx-include' => '#logs-search',
                    ]); ?>
                    <?= form_input('search[createdAtRange]', set_value('createdAtRange', $table['search']['createdAtRange'] ?? ''), [
                        'id' => 'dateRangePicker', 'class' => 'input input-bordered' . (set_value('createdAt', $table['search']['createdAt'] ?? '') === 'custom' ? '' : ' hidden'),
                        'hx-get' => site_url($table['helper']->baseURL()), 'hx-include' => '#logs-search',
                        'hx-trigger' => 'customDateRangeSelected',
                    ]); ?>
                </div>
            </form>
        </div>
        <?php if ($logs): ?>
            <div hx-boost="true">
                <?= $table['pager']->links(); ?>
            </div>
        <?php endif; ?>
    </div>

    <?php if ($logs): ?>
        <table class="table table-xs table-zebra w-full mt-6 p-6">
            <thead>
                <tr hx-boost="true">
                    <th>Moderator</th>
                    <th>Resource</th>
                    <th><?= anchor($table['helper']->sortByURL('resource_type'), 'Type'); ?> <?= $table['helper']->getSortIndicator('resource_type'); ?></th>
                    <th><?= anchor($table['helper']->sortByURL('status'), 'Status'); ?> <?= $table['helper']->getSortIndicator('status'); ?></th>
                    <th><?= anchor($table['helper']->sortByURL('created_at'), 'Created at'); ?> <?= $table['helper']->getSortIndicator('created_at'); ?></th>
                </tr>
            </thead>
            <tbody>
            <?php foreach ($logs as $log): ?>
                <tr class="hover">
                    <td>
                        <div class="flex items-center space-x-3">
                            <div class="avatar">
                                <div class="mask mask-squircle">
                                    <?= $log->author->renderAvatar(20); ?>
                                </div>
                            </div>
                            <div class="px-0">
                                <div class="font-bold cursor-pointer" hx-get="<?= esc($log->author->link(), 'attr') ?>"><?= esc($log->author->username); ?></div>
                                <div class="text-sm opacity-50"></div>
                            </div>
                        </div>
                    </td>
                    <td>
                        <?php if ($log->resource === null): ?>
                            <i>Permanently deleted</i>
                        <?php elseif ($log->resource->isDeleted()): ?>
                            <?= $log->resource->thread_title; ?>
                        <?php else: ?>
                            <a href="<?= $log->resource->link(); ?>" target="_blank"><?= $log->resource->thread_title; ?></a>
                        <?php endif; ?>
                    </td>
                    <td>
                        <?= ucfirst($log->resource_type); ?>
                    </td>
                    <td>
                        <small class="badge <?= $log->status === 'approved' ? 'badge-success' : 'badge-error'; ?>">
                            <?= ucfirst($log->status); ?>
                        </small>
                    </td>
                    <td>
                        <div class="cursor-help" title="<?= $log->created_at; ?>">
                            <?= $log->created_at->humanize(); ?>
                        </div>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>

        <div class="mt-6 text-center" hx-boost="true">
            <?= $table['pager']->links(); ?>
        </div>
    <?php else: ?>
        <div class="mt-6 p-6 border rounded bg-base-200 border-base-300">
            <p>Sorry, there are no logs to display.</p>
        </div>
    <?php endif; ?>


</div>

<?= $this->endSection() ?>
