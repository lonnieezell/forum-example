<?= $this->extend('master')  ?>

<?= $this->section('header') ?>
    <div class="header-row">
        <div>
            <h2 class="text-neutral-600">Members</h2>
        </div>
    </div>
<?= $this->endSection() ?>

<?= $this->section('main')  ?>
    <div id="member-list">

        <div class="mt-4 my-6 flex justify-between">
            <form action="<?= current_url(); ?>" id="member-search" class="flex flex-col sm:flex-row sm: w-full gap-1 sm:gap-4" hx-target="body">
                <div class="form-control">
                    <input
                        name="search[username]"
                        type="search"
                        class="input input-sm input-bordered w-auto"
                        placeholder="Search users..."
                        value="<?= set_value('username', $table['search']['username'] ?? ''); ?>"
                        hx-get="<?= site_url($table['helper']->baseURL()); ?>"
                        hx-include="#member-search"
                        hx-trigger="keyup changed delay:500ms"
                    >
                </div>
                <div class="form-control">
                    <input
                        name="search[country]"
                        type="search"
                        class="input input-sm input-bordered w-auto"
                        placeholder="By country..."
                        value="<?= set_value('country', $table['search']['country'] ?? ''); ?>"
                        hx-get="<?= site_url($table['helper']->baseURL()); ?>"
                        hx-include="#member-search"
                        hx-trigger="keyup changed delay:500ms"
                    >
                </div>
                <div class="form-control">
                    <?= form_dropdown('search[role]', $table['dropdowns']['role'], set_value('role', $table['search']['role'] ?? 'all'), [
                        'class' => 'filter-select select select-bordered select-sm w-auto',
                        'hx-get' => site_url($table['helper']->baseURL()),
                        'hx-include' => '#member-search'
                    ]); ?>
                </div>
                <div class="form-control">
                    <?= form_dropdown('search[type]', $table['dropdowns']['type'], set_value('type', $table['search']['type'] ?? 'all'), [
                        'class' => 'filter-select select select-bordered select-sm w-auto',
                        'hx-get' => site_url($table['helper']->baseURL()),
                        'hx-include' => '#member-search'
                    ]); ?>
                </div>
            </form>
        </div>

        <?php

                    use App\Libraries\CountryHelper;

 if ($members): ?>
            <div class="overflow-x-auto">
                <table class="table w-full mt-6 p-6">
                    <thead>
                        <tr hx-include="#member-search" hx-boost="true">
                            <th><?= anchor($table['helper']->sortByURL('username'), 'Username'); ?> <?= $table['helper']->getSortIndicator('username'); ?></th>
                            <th><?= anchor($table['helper']->sortByURL('count'), 'Posts'); ?> <?= $table['helper']->getSortIndicator('count'); ?></th>
                            <th><?= anchor($table['helper']->sortByURL('country'), 'Country'); ?> <?= $table['helper']->getSortIndicator('country'); ?></th>
                            <th><?= anchor($table['helper']->sortByURL('last_active'), 'Last posted'); ?> <?= $table['helper']->getSortIndicator('last_active'); ?></th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($members as $member): ?>
                        <tr class="hover">
                            <td>
                                <div class="flex items-center space-x-3">
                                    <div class="avatar">
                                        <div class="mask mask-squircle">
                                            <?= $member->renderAvatar(40); ?>
                                        </div>
                                    </div>
                                    <div class="px-4">
                                        <div class="font-bold cursor-pointer" hx-get="<?= esc($member->link(), 'attr') ?>"><?= esc($member->username); ?></div>
                                        <div class="text-sm opacity-50"><?= is_array($member->roles) ? implode(', ', $member->roles) : $member->roles; ?></div>
                                    </div>
                                </div>
                            </td>
                            <td><?= $member->count; ?></td>
                            <td><?= ($member->country !== null) ? esc(CountryHelper::fromCode($member->country)) : 'Unknown'; ?></td>
                            <td><?= ($member->last_active !== null) ? $member->last_active->humanize() : 'Never';?></td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

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
