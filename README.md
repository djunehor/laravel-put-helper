# Laravel PUT Helper
Laravel PUT helper is a package that helps you get input data, as well as uploaded files for PUT requests

- [Laravel PUT Helper](#laravel-sms)
    - [Installation](#installation)
        - [Laravel 5.5 and above](#laravel-55-and-above)
        - [Laravel 5.4 and older](#laravel-54-and-older)
        - [Lumen](#lumen)
    - [Usage](#usage)
    - [How it Works](#how-it-works)
    - [Contributing](#contributing)

## Installation
You can install the package via composer:

```shell
composer require djunehor/laravel-put-helper
```

#### Laravel 5.5 and above

The package will automatically register itself, so you can start using it immediately.

#### Laravel 5.4 and older

In Laravel version 5.4 and older, you have to add the service provider in `config/app.php` file manually:

```php
'providers' => [
    // ...
    Djunehor\PutHelper\PutHelperServiceProvider::class,
];
```
#### Lumen

After installing the package, you will have to register it in `bootstrap/app.php` file manually:
```php
// Register Service Providers
    // ...
    $app->register(Djunehor\PutHelper\PutHelperServiceProvider::class);
];
```

## Usage
After following the above installation instructions, no further action is required. Input data (string and files) will be available for all your PUT requests

In order to validate if a param is file, use `put_file` in your validation. For example:
```php
$request->validate([
'my_file' => 'required|put_file'
]);
```

## How it Works
The package registers a global middleware that intercepts all PUT request and tries to get the input payload via php raw input stream. It then merges the parsed input data to the request object so the inputs are available normally from wherever you're accessing the request object.
That is, if a file with field `my_file` is sent from the form, you can access via `$request->file`.

At the moment, `$request->file('file_key')` doesn't work. Use other methods instead e.g `$request->file_key`,` $request['file_key]`.
 
## Contributing
- Fork this project
- Clone to your repo
- Make your changes and run tests `composer test`
- Push and create Pull request
