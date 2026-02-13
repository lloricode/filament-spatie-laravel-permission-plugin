# Filament spatie laravel permission plugin

[![Latest Version on Packagist](https://img.shields.io/packagist/v/lloricode/filament-spatie-laravel-permission-plugin.svg?style=flat-square)](https://packagist.org/packages/lloricode/filament-spatie-laravel-permission-plugin)
[![GitHub Tests Action Status](https://img.shields.io/github/actions/workflow/status/lloricode/filament-spatie-laravel-permission-plugin/run-tests.yml?branch=main&label=tests&style=flat-square)](https://github.com/lloricode/filament-spatie-laravel-permission-plugin/actions?query=workflow%3Arun-tests+branch%3Amain)
[![Total Downloads](https://img.shields.io/packagist/dt/lloricode/filament-spatie-laravel-permission-plugin.svg?style=flat-square)](https://packagist.org/packages/lloricode/filament-spatie-laravel-permission-plugin)
[![codecov](https://codecov.io/gh/lloricode/filament-spatie-laravel-permission-plugin/graph/badge.svg?token=Z0BXJ4LVL3)](https://codecov.io/gh/lloricode/filament-spatie-laravel-permission-plugin)

## Requirements
- Laravel 11.20+ and up
- PHP 8.3+
- [Spatie/laravel-permission 6.9+ and up](https://github.com/spatie/laravel-permission/tree/6.9.0)
- Filament 3.2.98+ and up

## Pre-requisite

To be able to use this package, you need to have [spatie/laravel-permission v6.9+](https://github.com/spatie/laravel-permission/tree/6.9.0) installed.
Then follow the installation guide of spatie/laravel-permission.

This has used a [wildcard](https://spatie.be/docs/laravel-permission/v6/basic-usage/wildcard-permissions) permission based.
So you must to set this on your `config/permission.php` file, else it will throw an exception `Please enable wildcard permission in your config/permission.php`.

```php
// config/permission.php
'enable_wildcard_permission' => true,
```

## Installation

You can install the package via composer:

```bash
composer require lloricode/filament-spatie-laravel-permission-plugin
```


You can publish the config file with:

```bash
php artisan vendor:publish --tag="filament-permission-config"
```

This is the contents of the published config file:

https://github.com/lloricode/filament-spatie-laravel-permission-plugin/blob/main/config/filament-permission.php

## Usage

```php
use Lloricode\FilamentSpatieLaravelPermissionPlugin\FilamentPermissionPlugin;

->plugins([
    FilamentPermissionPlugin::make(),
])
```

### Setting up user model

```php
// ...
use Lloricode\FilamentSpatieLaravelPermissionPlugin\Concern\PermissionUser;
use Lloricode\FilamentSpatieLaravelPermissionPlugin\Contracts\HasPermissionUser;

class Admin extends Authenticatable implements HasPermissionUser // ...
{
    use PermissionUser;
// ...
```

### Setting up Filament pages

```php
// ...
use Lloricode\FilamentSpatieLaravelPermissionPlugin\Concern\PermissionPages;
use Lloricode\FilamentSpatieLaravelPermissionPlugin\Contracts\HasPermissionPages;

class MyPage extends Page implements HasPermissionPages
{
    use PermissionPages;
// ...
```

### Setting up Filament widgets

```php
// ...
use Lloricode\FilamentSpatieLaravelPermissionPlugin\Concern\PermissionWidgets;
use Lloricode\FilamentSpatieLaravelPermissionPlugin\Contracts\HasPermissionWidgets;

class MyWidget extends ChartWidget implements HasPermissionWidgets
{
    use PermissionWidgets;
// ...
```
Finally, run this command to generate the permission.

```bash
php artisan permission:sync
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
