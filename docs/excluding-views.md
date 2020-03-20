---
title: Excluding views
order: 3
---

# Excluding views

If you want to exclude certain views from being processed by Xray, you can do this by adding them to the configuration file.

Publish the configuration file using:

```shell
php artisan vendor:publish --provider=BeyondCode\\ViewXray\\ViewXrayServiceProvider
``` 

This will publish a file called `xray.php` in your `config` folder.

This is the content of the configuration file:

@verbatim
```php
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
@endverbatim