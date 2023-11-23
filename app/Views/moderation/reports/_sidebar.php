<ul class="menu bg-base-200 w-56 rounded-box" hx-boost="true">
    <li>
        <h2 class="menu-title">Moderation queue</h2>
        <ul>
            <?= view_cell('Moderation/SubMenuCell', ['userId' => user_id()]) ?>
        </ul>
    </li>
</ul>
