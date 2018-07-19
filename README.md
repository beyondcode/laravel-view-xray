# Xray - Take a look into your Laravel views

[![Latest Version on Packagist](https://img.shields.io/packagist/v/beyondcode/laravel-view-xray.svg?style=flat-square)](https://packagist.org/packages/beyondcode/laravel-view-xray)
[![Build Status](https://img.shields.io/travis/beyondcode/laravel-view-xray/master.svg?style=flat-square)](https://travis-ci.org/beyondcode/laravel-view-xray)
[![Quality Score](https://img.shields.io/scrutinizer/g/beyondcode/laravel-view-xray.svg?style=flat-square)](https://scrutinizer-ci.com/g/beyondcode/laravel-view-xray)
[![Total Downloads](https://img.shields.io/packagist/dt/beyondcode/laravel-view-xray.svg?style=flat-square)](https://packagist.org/packages/beyondcode/laravel-view-xray)

When your Laravel project grows, so do the Laravel views. Sometimes it might be hard to figure out, which part of the output HTML was rendered using which template.
With this package, you can take a peek into your Laravel views and find out which template is responsible for which part of the output HTML.

![Example output](https://beyondco.de/github/xray/xray.png)

## Installation

You can install the package via composer:

```bash
composer require beyondcode/laravel-view-xray --dev
```

The package is enabled by default - so all you need to do is visit your application in the browser and hit the Xray shortcut.

Windows: CTRL + Shift + X
OSX: CMD + Shift + X

This toggles the Xray view, where you can see which view (and optionally section) rendered the visual output.

## Disabling Xray

You can disable Xray by setting an environment variable called `XRAY_ENABLED` to `false`.

## Excluding views

If you want to exclude certain views from being processed by Xray, you can do this by adding them to the configuration file.

Publish the configuration file using:

```bash
php artisan vendor:publish --provider=BeyondCode\\ViewXray\\ViewXrayServiceProvider
``` 

This will publish a file called `xray.php` in your `config` folder.

This is the content of the configuration file:

```php
<?php

return [

    /*
     * Determines if the Xray package should be enabled.
     */
    'enabled' => env('XRAY_ENABLED', true),

    /*
     * If you want to exclude certain views from being processed by Xray,
     * you can list them here. Be aware that the check only applies to the
     * root views that you add here. If these views include other views
     * themselves, they need to be excluded manually.
     */
    'excluded' => [
        //
    ],

];
```

Just place the view names that you want to exclude in the `excluded` array.

## FAQ

- Does this work with VueJS too?

Yes, this package does work in combination with VueJS, but you need to enable comments in your VueJS app. This package works by adding specific HTML comments into the rendered views and uses
these comments to find the bounding boxes of the rendered HTML. VueJS by default removes HTML comments prior to rendering.

You can enable HTML comments in your VueJS app by setting `comments` to `true`:

```javascript
let app = new Vue({
    el: '#content',
    comments: true
});
```

### Testing

``` bash
composer test
```

### Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information what has changed recently.

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

### Security

If you discover any security related issues, please email marcel@beyondco.de instead of using the issue tracker.

## Credits

This project is inspired by [xray-rails](https://github.com/brentd/xray-rails).

- [Marcel Pociot](https://github.com/:author_username)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
