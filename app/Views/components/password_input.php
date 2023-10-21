<div x-data="{ show: false }">
    <div class="relative">
        <input :type="show ? 'text' : 'password'"
            name="<?= $name ?? 'password' ?>"
            type="password"
            placeholder="<?= $placeholder ?? 'Password' ?>"
            class="<?= $class ?? '' ?> mt-1 input input-bordered w-full"
            style="padding-right: 3rem;"
        />

        <div class="absolute" style="right: 1rem; top: 1rem;">
            <span @click="show = !show" :class="{'block': !show, 'hidden':show }">
                <?= view('icons/eye') ?>
            </span>
            <span @click="show = !show" :class="{'hidden': !show, 'block':show }">
                <?= view('icons/eye-slash') ?>
            </span>
        </div>
    </div>
</div>
