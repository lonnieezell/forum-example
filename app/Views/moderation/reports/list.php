<?= $this->extend('master')  ?>

<?= $this->section('sidebar')  ?>
    <?= view('moderation/reports/_sidebar'); ?>
<?= $this->endSection() ?>

<?= $this->section('main')  ?>

<h1>Moderation queue - <?= plural(ucfirst($table['resourceType'])); ?></h1>

<div id="report-list" x-data="{
            selectAll: false,
            showOptions: false,
            items: [],
            toggleAll() {
                this.selectAll = !this.selectAll

                let checkboxes = document.querySelectorAll('.selectable-item');
                let selectedItems = [];

                [...checkboxes].map((el) => {
                    el.checked = this.selectAll;
                    selectedItems.push(el.value);
                })

                this.items = this.selectAll ? selectedItems : [];
                this.showOptions = this.items.length > 0;
            },
            toggle(id) {
                if (this.items.includes(id)) {
                    const index = this.items.indexOf(id);
                    this.items.splice(index, 1);
                } else {
                    this.items.push(id);
                }
                this.showOptions = this.items.length > 0;
            }
        }">

    <div class="mt-4 my-6 flex justify-between">
        <div class="flex">
            <div x-show="showOptions">
                <form hx-post="<?= route_to('moderate-action', $table['resourceType']); ?>"
                      hx-include=".selectable-item"
                      hx-confirm="Are you sure you want to launch this mass action?"
                >
                    <?= csrf_field(); ?>
                    <div class="join">
                        <?= form_dropdown('action', [
                            'approve' => 'Approve selected',
                            'deny' => 'Deny selected',
                            'ignore' => 'Ignore selected',
                        ], set_value('action'), ['class' => 'select join-item']); ?>
                        <button type="submit" class="btn join-item">Go</button>
                    </div>
                </form>
            </div>
        </div>
        <?php if ($reports): ?>
            <div hx-boost="true">
                <?= $table['pager']->links(); ?>
            </div>
        <?php endif; ?>
    </div>

    <?php if ($reports): ?>
        <table class="table table-xs table-zebra w-full mt-6 p-6">
            <thead>
            <tr hx-boost="true">
                <th>
                    <label>
                        <input type="checkbox" class="checkbox" @click="toggleAll()" x-bind:checked="selectAll" />
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
                            <input type="checkbox" name="items[]" class="checkbox selectable-item" @click="toggle(<?= $report->id; ?>)" value="<?= $report->id; ?>" />
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
                        <?= form_open(route_to('moderate-action', $report->resource_type), [
                            'hx-post' => route_to('moderate-action', $report->resource_type),
                            'class' => 'mx-1'
                        ]); ?>
                            <input type="hidden" name="action" value="approve">
                            <input type="hidden" name="items[]" value="<?= $report->id; ?>">
                            <button type="submit" class="btn btn-xs btn-success" title="Approve this post">Approve</button>
                        <?= form_close(); ?>

                        <?= form_open(route_to('moderate-action', $report->resource_type), [
                            'hx-post' => route_to('moderate-action', $report->resource_type),
                            'class' => 'mx-1'
                        ]); ?>
                            <input type="hidden" name="action" value="deny">
                            <input type="hidden" name="items[]" value="<?= $report->id; ?>">
                            <button type="submit" class="btn btn-xs btn-error" title="Deny this post">Deny</button>
                        <?= form_close(); ?>

                        <?= form_open(route_to('moderate-action', $report->resource_type), [
                            'hx-post' => route_to('moderate-action', $report->resource_type),
                            'class' => 'mx-1'
                        ]); ?>
                            <input type="hidden" name="action" value="ignore">
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
                        <?php if (isset($report->resource->tags) && ! $report->resource->tags->isEmpty()): ?>
                            <?= view('discussions/tags/_thread', ['tags' => $report->resource->tags]) ?>
                        <?php endif; ?>
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
