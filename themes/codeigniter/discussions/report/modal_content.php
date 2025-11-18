<?= form_open('', ['hx-post' => current_url(), 'hx-swap' => 'outerHTML']); ?>
    <h3 class="font-bold text-lg">Report <?= ucfirst($type); ?></h3>
    <div class="form-control mt-2">
        <label class="label">
            <span class="label-text">Reason</span>
        </label>
        <textarea name="comment" class="textarea textarea-bordered h-24" maxlength="255" required
                  placeholder="Type the reason here..."><?= set_value('comment'); ?></textarea>
    </div>
    <div class="modal-action">
        <button type="button" class="btn" onclick="document.getElementById('modal-dialog').close()">Close</button>
        <button type="submit" class="btn btn-primary">Send</button>
    </div>
<?= form_close(); ?>
