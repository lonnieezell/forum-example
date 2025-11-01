# Themes

The theme system is based on the standard CodeIgniter View system with layouts. The primary difference is that themes are stored in a separate folder. This allows you to have multiple themes in your application and switch between them easily.

## Theme Storage and Organization

Themes are stored in the themes folder. Each theme has its own folder. The default theme is default. The theme stores the layout files, the assets, and the fragment views used.

The views stored within the theme folder are only the view fragments needed for the main user-facing content that might need to be changed between themes. This does NOT include the views for the user account pages, the moderation or admin pages. These are stored in the Views folder like normal.

## Available Themes

### Admin Theme

The admin theme provides the interface for forum administrators and moderators. It includes:

- Admin dashboard views
- Moderation tools
- Management interfaces
- Located in: admin

### Default Theme

The default public-facing theme displays the forum to regular users. It includes:

- Category listings
- Thread views
- User profiles
- Located in: default

## Theme Directory Structure

Each theme is organized like this:

```
themes/
├── admin/
│   ├── css/
│   │   └── admin.css
│   ├── js/
│   │   └── admin.js
│   └── views/
│       └── [view files]
└── default/
    ├── css/
    │   └── style.css
    ├── js/
    │   └── app.js
    ├── images/
    └── views/
        └── [view files]
```

- css/ - Stylesheets for the theme
- js/ - JavaScript files specific to the theme
- images/ - Images used by the theme
- views/ - Blade/template files for rendering content

## Setting the Active Theme

The default theme used is set in the `app/Config/Forum.php` file:

```php
public string $themeName = 'default';
```

This is then used by the `BaseController` to set the active theme for rendering views. You can change this value within each controller by setting the `$theme` property on the controller to the name of the theme's folder you wish to use.

```php
class SomeController extends BaseController
{
    protected string $theme = 'admin';
}
```

## Referencing Other Themed Views

To include other views from the theme, you can use `$this->view()` instead of `view()`.

```php
<?= $this->view('dicsussions/_list_item', ['thread' => $thread]) ?>
```
