# Modules

Modules are the core building blocks of the Vox forum application. Each module encapsulates a specific feature or set of related features, allowing for a modular and extensible architecture. This is built on top of CodeIgniter 4's modular support and extends it to allow for dynamic enabling/disabling of modules as well as addon modules.

## Module Structure

Each module is defined by a specific directory structure, typically containing the following components:

- **Controllers**: Handle incoming requests and return responses.
- **Models**: Interact with the database and define the data structure.
- **Views**: Define the presentation layer and user interface.
- **Routes**: Define the URL endpoints for the module.

## Module Management

The application provides a set of tools for managing modules, including:

- **Discovery**: Automatically detect and register modules.
- **Dependency Management**: Handle module dependencies and loading order.
- **Caching**: Improve performance by caching module metadata.

## Creating a New Module

To create a new module, follow these steps:

1. Define the module directory structure.
2. Create the necessary controllers, models, and views.
3. Create the `module.php` manifest file to define module metadata.

### Example `module.php` Manifest

```php
return [
    'name'          => 'core-discussions',
    'version'       => '1.0.0',
    'description'   => 'Handles user posts and comments',
    'namespace'     => 'Vox\Discussions',
    'dependencies'  => ['core-discussions'],
    'permissions'   => ['posts.create' => '...'],
];
```
