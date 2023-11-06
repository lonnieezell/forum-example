# FileSystems

The `Storage` class provides a simple wrapper around the [FlySystem](https://flysystem.thephpleague.com/) library. This enables you to easily store and retrieve files from a variety of storage providers.

## Configuration

The `Storage` class is configured via the `config/filesystems.php` file. This file contains an array of named disks, each of which may provide a different storage provider. The `default` disk option may be used to specify which disk should be used if no other disk is specified.

Each disk must have the `driver` listed, which represents the FlySystem driver to use. At the moment, this only supports `local` and `s3` as this is a very lean implementation, and not intended to be a thorough wrapper around FlySystem.

## Basic Usage

The storage class is available as a service and can be readily used within your classes.

```php
// Get a FlySystem adapter for the specified disk
$storage = service('storage')->disk('default');

try {
    $storage->write($path, $contents);
} catch (FilesystemException | UnableToWriteFile $exception) {
    // handle the error
}
```

The service's `disk()` method will return a FlySystem adapter instance ready for your usage. If no disk is specified, it will return the default disk.
