<footer class="main-footer bg-base-300 py-12 flex flex-col justify-center">
    <div class="flex flex-col container m-auto">

        <div class="w-full">
            <h2 class="text-base-content text-xl font-semibold">
                <a href="<?= site_url('/') ?>">
                    <?= config('App')->siteName ?>
                </a>
            </h2>
        </div>

        <div class="mt-6">
            <p class="text-base">
                <?php if (! empty(config('App')->copyrightHolder)) : ?>
                    Copyright <?= date('Y') ?> <?= config('App')->copyrightHolder ?> - All Rights Reserved
                <?php endif ?>
                <span class="pl-8 pr-4">
                    <a href="<?= url_to('terms') ?>">Terms of Service</a>
                </span>
                <span class="px-4">
                    <a href="<?= url_to('privacy') ?>">Privacy Policy</a>
                </span>
            </p>

            <p class="text-sm mt-2">
                Generated in <?= ('{elapsed_time}') ?> seconds<?= function_exists('sys_getloadavg') ? ', with a server load of '. number_format(sys_getloadavg()[1], 2) : '.' ?>
            </p>
        </div>

    </div>
</footer>
