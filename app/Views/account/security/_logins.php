<div class="p-6 bg-base-200 border border-l-0 border-r-0 border-base-300">
    <h2 class="text-lg font-semibold pb-4">Recent Logins</h2>

    <div class="overflow-x-auto">
        <table class="table table-sm">
            <thead>
                <tr>
                    <th class="text-start">Date</th>
                    <th>IP Address</th>
                    <th>User Agent</th>
                    <th class="w-6">Success</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($logins as $login) : ?>
                <tr>
                    <td><?= $login->date?->format('F d, Y g:ia T') ?></td>
                    <td><?= $login->ip_address ?? 'n/a' ?></td>
                    <td>
                        <?php if ($login->user_agent) : ?>
                            <?= $agent->getPlatform() ?> -
                            <?= $agent->getBrowser() ?>
                        <?php endif ?>
                    </td>
                    <td class="text-center">
                        <?php if ($login->success) : ?>
                            &check;
                        <?php else : ?>
                            &#9866;
                        <?php endif ?>
                    </td>
                </tr>
                <?php endforeach ?>
            </tbody>
        </table>
    </div>
</div>
