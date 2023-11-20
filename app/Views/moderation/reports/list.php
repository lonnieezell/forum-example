<?= $this->extend('master')  ?>

<?= $this->section('main')  ?>

<h1>Moderation queue - <?= plural(ucfirst($table['resourceType'])); ?></h1>

<div id="report-list">

    <div class="mt-4 my-6 flex justify-between">
        <?php if ($reports): ?>
            <div class="mt-6" hx-boost="true">
                <?= $table['pager']->links(); ?>
            </div>
        <?php endif; ?>
    </div>

    <?php if ($reports): ?>
        <table class="table table-xs table-zebra w-full mt-6 p-6" x-data="{
            selectAll: false,
            toggleAllCheckboxes() {
                this.selectAll = !this.selectAll

                checkboxes = document.querySelectorAll('.selectable-item');
                [...checkboxes].map((el) => {
                    el.checked = this.selectAll;
                })
            }
        }">
            <thead>
            <tr hx-boost="true">
                <th>
                    <label>
                        <input type="checkbox" class="checkbox" @click="toggleAllCheckboxes()" x-bind:checked="selectAll"  />
                    </label>
                </th>
                <th>Reporter</th>
                <th>Reason</th>
                <th><?= anchor($table['helper']->sortByURL('created_at'), 'Created at'); ?> <?= $table['helper']->getSortIndicator('created_at'); ?></th>
                <th>Actions</th>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($reports as $report): ?>
                <tr class="hover">
                    <th rowspan="2">
                        <label>
                            <input type="checkbox" name="items" class="checkbox selectable-item" value="<?= $report->id; ?>" />
                        </label>
                    </th>
                    <td>
                        <div class="flex items-center space-x-3">
                            <div class="avatar">
                                <div class="mask mask-squircle">
                                    <?= $report->author->renderAvatar(20); ?>
                                </div>
                            </div>
                            <div class="px-0">
                                <div class="font-bold cursor-pointer" hx-get="<?= esc($report->author->link(), 'attr') ?>"><?= esc($report->author->username); ?></div>
                                <div class="text-sm opacity-50"></div>
                            </div>
                        </div>
                    </td>
                    <td>
                        <div class="tooltip cursor-help" data-tip="<?= esc($report->comment, 'attr'); ?>">
                            <?= ellipsize(esc($report->comment), 50); ?>
                        </div>
                    </td>
                    <td><?= $report->created_at->humanize(); ?></td>
                    <td class="flex">
                        <?= form_open(route_to('moderate-action', $report->resource_type, 'approve'), [
                            'hx-post' => route_to('moderate-action', $report->resource_type, 'approve'),
                            'class' => 'mx-1'
                        ]); ?>
                            <input type="hidden" name="items[]" value="<?= $report->id; ?>">
                            <button type="submit" class="btn btn-xs btn-success" title="Approve this post">Approve</button>
                        <?= form_close(); ?>

                        <?= form_open(route_to('moderate-action', $report->resource_type, 'deny'), [
                            'hx-post' => route_to('moderate-action', $report->resource_type, 'deny'),
                            'class' => 'mx-1'
                        ]); ?>
                            <input type="hidden" name="items[]" value="<?= $report->id; ?>">
                            <button type="submit" class="btn btn-xs btn-error" title="Deny this post">Deny</button>
                        <?= form_close(); ?>

                        <?= form_open(route_to('moderate-action', $report->resource_type, 'ignore'), [
                            'hx-post' => route_to('moderate-action', $report->resource_type, 'ignore'),
                            'class' => 'mx-1'
                        ]); ?>
                            <input type="hidden" name="items[]" value="<?= $report->id; ?>">
                            <button type="submit" class="btn btn-xs" title="Ignore this post">Ignore</button>
                        <?= form_close(); ?>
                    </td>
                </tr>
                <tr>
                    <td colspan="4" class="px-4 py-3">
                        <div class="flex mb-2">
                            <?php if (isset($report->resource->author)): ?>
                                <a href="<?= $report->resource->author->link() ?>">
                                    <b><?= esc($report->resource->author->username) ?></b>
                                </a>
                                <i class="mx-2"><?= $report->resource->created_at->humanize(); ?></i>
                            <?php endif; ?>
                            <div class="flex-auto text-right">
                                <a href="<?= $report->resource->link() ?>" target="_blank">
                                    Thread: <strong><?= esc($report->resource->thread_title); ?></strong> &bull;
                                    Category: <strong><?= esc($report->resource->category_title); ?></strong>
                                </a>
                            </div>
                        </div>
                        <?= $report->resource->render(); ?>
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
            <p>Sorry, there are no reports to display.</p>
        </div>
    <?php endif; ?>


</div>

<?= $this->endSection() ?>
