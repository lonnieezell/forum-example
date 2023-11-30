# Themes

The theme system is based on the standard CodeIgniter View system with layouts. The primary difference is that themes are stored in a separate folder. This allows you to have multiple themes in your application and switch between them easily.

They are stored in the `themes` folder. Each theme has its own folder. The default theme is `default`. The theme stores the layout files, the assets, and the fragment views used.

The views stored within the theme folder are only the view fragments needed for the main user-facing content that might need to be changed between themes. This does NOT include the views for the user account pages, the moderation or admin pages. These are stored in the `app/Views` folder like normal.

## Referencing Other Themed Views

To include other views from the theme, you can use `$this->view()` instead of `view()`.

```php
<?= $this->view('dicsussions/_list_item', ['thread' => $thread]) ?>
```
