# This is my package filament-spatie-laravel-permission-plugin

[![Latest Version on Packagist](https://img.shields.io/packagist/v/lloricode/filament-spatie-laravel-permission-plugin.svg?style=flat-square)](https://packagist.org/packages/lloricode/filament-spatie-laravel-permission-plugin)
[![GitHub Tests Action Status](https://img.shields.io/github/actions/workflow/status/lloricode/filament-spatie-laravel-permission-plugin/run-tests.yml?branch=main&label=tests&style=flat-square)](https://github.com/lloricode/filament-spatie-laravel-permission-plugin/actions?query=workflow%3Arun-tests+branch%3Amain)
[![GitHub Code Style Action Status](https://img.shields.io/github/actions/workflow/status/lloricode/filament-spatie-laravel-permission-plugin/fix-php-code-styling.yml?branch=main&label=code%20style&style=flat-square)](https://github.com/lloricode/filament-spatie-laravel-permission-plugin/actions?query=workflow%3A"Fix+PHP+code+styling"+branch%3Amain)
[![Total Downloads](https://img.shields.io/packagist/dt/lloricode/filament-spatie-laravel-permission-plugin.svg?style=flat-square)](https://packagist.org/packages/lloricode/filament-spatie-laravel-permission-plugin)



This is where your description should go. Limit it to a paragraph or two. Consider adding a small example.

## Installation

You can install the package via composer:

```bash
composer require lloricode/filament-spatie-laravel-permission-plugin
```


You can publish the config file with:

```bash
php artisan vendor:publish --tag="filament-spatie-laravel-permission-plugin-config"
```

This is the contents of the published config file:

```php
<?php

declare(strict_types=1);

return [
    'roles' => [
        'super_admin' => 'super_admin',
        'admin' => 'admin',
    ],
    'extra_roles' => [

    ],

    'seeders' => [
        'roles' => \Lloricode\FilamentSpatieLaravelPermissionPlugin\Database\Seeders\DefaultRoleSeeder::class,
        'permissions' => \Lloricode\FilamentSpatieLaravelPermissionPlugin\Database\Seeders\DefaultPermissionSeeder::class,
    ],
];

```

## Usage

```php
use Lloricode\FilamentSpatieLaravelPermissionPlugin\FilamentPermissionPlugin;

->plugins([
    FilamentPermissionPlugin::make(),
])
```

## Testing

```bash
composer test
```

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Contributing

Please see [CONTRIBUTING](.github/CONTRIBUTING.md) for details.

## Security Vulnerabilities

Please review [our security policy](../../security/policy) on how to report security vulnerabilities.

## Credits

- [Lloric Mayuga Garcia](https://github.com/lloricode)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
