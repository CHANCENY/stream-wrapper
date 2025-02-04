# Stream Wrapper Library

This library provides a custom PHP **Stream Wrapper**, allowing you to register and use custom stream protocols (`public://` and `global://`) for file operations like reading and writing.

## Installation

1. **Clone the repository** or download the package into your project.
2. **Install dependencies** using Composer:

   ```sh
   composer require simp/streamwrapper
   ```

## Usage

### 1. Registering Custom Stream Wrappers

The package uses `WrapperRegister` to register custom stream wrappers.

```php
use Simp\StreamWrapper\Stream\GlobalStreamWrapper;
use Simp\StreamWrapper\WrapperRegister\WrapperRegister;

require_once "vendor/autoload.php";
require_once "PublicWrapper.php";

// Registering the global and public stream wrappers
WrapperRegister::register("global", GlobalStreamWrapper::class);
WrapperRegister::register("public", PublicWrapper::class);
```

### 2. Using the Registered Wrappers

Once registered, you can perform file operations using `global://` and `public://` as stream wrappers.

```php
// Writing and reading using global wrapper
file_put_contents("global://example.txt", "\nLorem ipsum dolor sit amet\n", FILE_APPEND);
echo file_get_contents("global://example.txt");

// Writing and reading using public wrapper
file_put_contents("public://example.txt", "\nHello World!\n", FILE_APPEND);
echo file_get_contents("public://example.txt");
```

### 3. Customizing the Public Wrapper

The `PublicWrapper.php` file extends the base `StreamWrapper` class, setting a custom `$stream_name` and `$base_path`:

```php
class PublicWrapper extends \Simp\StreamWrapper\Stream\StreamWrapper
{
    protected string $stream_name = "public";
    protected string $basePath = "sites/public";
}
```

Modify the `$basePath` to change where files are stored when using `public://` paths.

## Requirements

- PHP 8.0 or higher
- Composer for dependency management

## License

This project is licensed under the MIT License.
