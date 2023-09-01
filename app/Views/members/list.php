<?= $this->extend('master')  ?>

<?= $this->section('main')  ?>
    <h1>Members</h1>

    <div class="member-list">

        <div class="mt-4 my-6 flex justify-between">
            <form action="<?= current_url(); ?>" id="member-search" class="flex w-full" hx-target="body">
                <div class="form-control">
                    <label class="label">
                        <small class="label-text">Search by username:</small>
                    </label>
                    <input name="search[username]" type="search" class="input input-bordered w-auto" placeholder="Type username..." value="<?= set_value('username', $table['search']['username'] ?? ''); ?>"
                           hx-get="<?= site_url($table['helper']->baseURL()); ?>" hx-include="#member-search" hx-trigger="keyup changed delay:500ms">
                </div>
                <div class="form-control">
                    <label class="label">
                        <small class="label-text">By country:</small>
                    </label>
                    <input name="search[country]" type="search" class="input input-bordered w-auto" placeholder="Type country..." value="<?= set_value('country', $table['search']['country'] ?? ''); ?>"
                           hx-get="<?= site_url($table['helper']->baseURL()); ?>" hx-include="#member-search" hx-trigger="keyup changed delay:500ms">
                </div>
                <div class="form-control">
                    <label class="label">
                        <small class="label-text">User role:</small>
                    </label>
                    <?= form_dropdown('search[role]', $table['dropdowns']['role'], set_value('role', $table['search']['role'] ?? 'all'), [
                        'class' => 'select w-auto', 'hx-get' => site_url($table['helper']->baseURL()), 'hx-include' => '#member-search'
                    ]); ?>
                </div>
                <div class="form-control">
                    <label class="label">
                        <small class="label-text">User type:</small>
                    </label>
                    <?= form_dropdown('search[type]', $table['dropdowns']['type'], set_value('type', $table['search']['type'] ?? 'all'), [
                        'class' => 'select w-auto', 'hx-get' => site_url($table['helper']->baseURL()), 'hx-include' => '#member-search'
                    ]); ?>
                </div>
            </form>
            <?php if ($members): ?>
                <div class="mt-6" hx-boost="true">
                    <?= $table['pager']->links(); ?>
                </div>
            <?php endif; ?>
        </div>

        <?php if ($members): ?>
            <table class="table w-full mt-6 p-6">
                <thead>
                <tr hx-include="#member-search" hx-boost="true">
                    <th><?= anchor($table['helper']->sortByURL('username'), 'Username'); ?> <?= $table['helper']->getSortIndicator('username'); ?></th>
                    <th><?= anchor($table['helper']->sortByURL('count'), 'Posts'); ?> <?= $table['helper']->getSortIndicator('count'); ?></th>
                    <th><?= anchor($table['helper']->sortByURL('country'), 'Country'); ?> <?= $table['helper']->getSortIndicator('country'); ?></th>
                    <th><?= anchor($table['helper']->sortByURL('last_active'), 'Last post at'); ?> <?= $table['helper']->getSortIndicator('last_active'); ?></th>
                </tr>
                </thead>
                <tbody>
                <?php foreach ($members as $member): ?>
                    <tr>
                        <td>
                            <div class="flex items-center space-x-3">
                                <div class="avatar">
                                    <div class="mask mask-squircle w-12 h-12">
                                        <img src="<?= $member->avatarLink(40); ?>" alt="" />
                                    </div>
                                </div>
                                <div class="px-4">
                                    <div class="font-bold cursor-pointer" hx-get="<?= esc($member->link(), 'attr') ?>"><?= esc($member->username); ?></div>
                                    <div class="text-sm opacity-50"><?= is_array($member->roles) ? implode(', ', $member->roles) : $member->roles; ?></div>
                                </div>
                            </div>
                        </td>
                        <td><?= $member->count; ?></td>
                        <td><?= esc($member->country); ?></td>
                        <td><?= $member->last_active->humanize() ?? 'Never'; ?></td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>

            <div class="mt-6 text-center" hx-boost="true">
                <?= $table['pager']->links(); ?>
            </div>
        <?php else: ?>
            <div class="mt-6 p-6 border rounded bg-base-200 border-base-300">
                <p>Sorry, there are no users to display.</p>
            </div>
        <?php endif; ?>


    </div>

<?= $this->endSection() ?>
