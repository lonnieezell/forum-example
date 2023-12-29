<div class="mt-12 p-4 bg-base-300 text-sm text-center">
    <p>Generated in {elapsed_time} seconds</p>
    <p>
        Running CodeIgniter <?= CodeIgniter\CodeIgniter::CI_VERSION ?>
        on PHP <?= phpversion() ?>
        and <?= config('database')->default['DBDriver'] ?>
    </p>
</div>
