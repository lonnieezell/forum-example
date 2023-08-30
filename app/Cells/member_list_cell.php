<div class="mt-4 my-6 flex justify-between">
    <form action="<?= current_url(); ?>" id="member-search" class="flex w-full" hx-target="body">
        <div class="form-control">
            <label class="label">
                <small class="label-text">Search by username:</small>
            </label>
            <input name="search[user]" type="search" class="input input-bordered w-auto" placeholder="Type username..." value="<?= set_value('user', $this->search['user']); ?>"
                   hx-get="<?= site_url($this->baseURL()); ?>" hx-include="#member-search" hx-trigger="keyup changed delay:500ms">
        </div>
        <div class="form-control">
            <label class="label">
                <small class="label-text">By country:</small>
            </label>
            <input name="search[country]" type="search" class="input input-bordered w-auto" placeholder="Type country..." value="<?= set_value('country', $this->search['country']); ?>"
                   hx-get="<?= site_url($this->baseURL()); ?>" hx-include="#member-search" hx-trigger="keyup changed delay:500ms">
        </div>
        <div class="form-control">
            <label class="label">
                <small class="label-text">User role:</small>
            </label>
            <?= form_dropdown('search[role]', $this->validRoles(), set_value('role', $this->search['role']), [
                'class' => 'select w-auto', 'hx-get' => site_url($this->baseURL()), 'hx-include' => '#member-search'
            ]); ?>
        </div>
        <div class="form-control">
            <label class="label">
                <small class="label-text">User type:</small>
            </label>
            <?= form_dropdown('search[type]', $this->validTypes(), set_value('type', $this->search['type']), [
                'class' => 'select w-auto', 'hx-get' => site_url($this->baseURL()), 'hx-include' => '#member-search'
            ]); ?>
        </div>
    </form>
    <?php if ($members): ?>
        <div class="mt-6" hx-boost="true">
            <?= $pager->links(); ?>
        </div>
    <?php endif; ?>
</div>

<?php if ($members): ?>
    <table class="table w-full mt-6 p-6">
        <thead>
            <tr hx-include="#member-search" hx-boost="true">
                <th><?= anchor($this->sortByURL('username'), 'Username'); ?> <?= $this->getSortIndicator('username'); ?></th>
                <th><?= anchor($this->sortByURL('count'), 'Posts'); ?> <?= $this->getSortIndicator('count'); ?></th>
                <th><?= anchor($this->sortByURL('country'), 'Country'); ?> <?= $this->getSortIndicator('country'); ?></th>
                <th><?= anchor($this->sortByURL('last_active'), 'Last post at'); ?> <?= $this->getSortIndicator('last_active'); ?></th>
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
                            <div class="text-sm opacity-50"><?= esc($member->role); ?></div>
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
        <?= $pager->links(); ?>
    </div>
<?php else: ?>
    <div class="mt-6 p-6 border rounded bg-base-200 border-base-300">
        <p>Sorry, there are no users to display.</p>
    </div>
<?php endif; ?>
