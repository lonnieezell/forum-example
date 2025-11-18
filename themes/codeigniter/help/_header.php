<div class="hero bg-base-200 p-6">
    <div class="hero-content text-center">
        <div class="max-w-max">
            <?php if (empty($page)): ?>
                <h1 class="text-5xl font-bold">Help Section</h1>
                <p class="py-6">Provident cupiditate voluptatem et in. Quaerat fugiat ut assumenda excepturi exercitationem quasi.</p>
                <?= form_open('', [
                    'hx-post' => current_url(),
                    'hx-target' => '#help-content-container',
                ]); ?>
                    <div class="form-control">
                        <div class="input-group flex-auto">
                            <input type="text" name="search" value="<?= set_value('search', $search ?? ''); ?>" maxlength="32"
                                   placeholder="Search..." class="input input-bordered w-full">
                            <button class="btn btn-square border border-base-300" type="submit" data-loading-disable>
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" /></svg>
                            </button>
                        </div>
                    </div>
                <?= form_close(); ?>
            <?php else: ?>
                <h1 class="text-5xl font-bold"><?= $page->getName(); ?></h1>
            <?php endif; ?>
        </div>
    </div>
</div>
