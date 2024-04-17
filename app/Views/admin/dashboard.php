<?= $this->extend('master') ?>

<?= $this->section('main') ?>

    <div class="container">

        <div class="flex gap-12">
            <div class="w-1/2">
                <h2 class="text-xl font-semibold">App and Server Statistics</h2>

                <div class="overflow-x-auto">
                    <table class="table table-zebra">
                        <tbody>
                            <tr>
                                <td>CodeIgniter Version</td>
                                <td><?= CodeIgniter\CodeIgniter::CI_VERSION ?></td>
                            </tr>
                            <tr>
                                <td>PHP Version</td>
                                <td><?= phpversion() ?></td>
                            </tr>
                            <tr>
                                <td>Database Driver</td>
                                <td><?= config('database')->default['DBDriver'] ?></td>
                            </tr>
                            <tr>
                                <td>Database Server Version</td>
                                <td><?= db_connect()->getVersion() ?></td>
                            </tr>
                            <tr>
                                <td>Server Load</td>
                                <td>
                                    <?= function_exists('sys_getloadavg')
                                        ? number_format(sys_getloadavg()[1], 2)
                                        : 'Not available'
                                    ?>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="w-1/2">
                <h2 class="text-xl font-semibold">Forum Statistics</h2>
                <div class="overflow-x-auto">
                    <table class="table table-zebra">
                        <tr>
                            <td>Threads</td>
                            <td>
                                <?= number_format($threadCount) ?>
                                <br><?= number_format($todaysThreadCount) ?> new today
                                <br><a href="<?= url_to('moderate-threads') ?>" class="link">
                                        <?= number_format($reportedThreadCount) ?> reported
                                    </a>
                            </td>
                        </tr>
                        <tr>
                            <td>Posts</td>
                            <td>
                                <?= number_format($postCount) ?>
                                <br><?= number_format($todaysPostCount) ?> new today
                                <br><a href="<?= url_to('moderate-posts') ?>" class="link">
                                        <?= number_format($reportedPostCount) ?> reported
                                    </a>
                            </td>
                        </tr>
                        <tr>
                            <td>Users</td>
                            <td>
                                <?= number_format($userCount) ?>
                                <br><?= number_format($activeUserCount) ?> active
                                <br><?= number_format($todaysUserCount) ?> new today
                            </td>
                        </tr>
                        <tr>
                            <td>Images</td>
                            <td>
                                <?= number_format($imageCount) ?>
                                <br><?= number_format($imageSize / 1024 / 1024, 2) ?> MB used
                            </td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>


    </div>
<?= $this->endSection() ?>
