---
title: Disabling Xray
order: 2
---

## Disabling Xray

You can disable Xray by setting an environment variable called `XRAY_ENABLED` to `false`.

If you want to customize how xray should be enabled/disabled, you can publish the package configuration file using:

```bash
php artisan vendor:publish --provider=BeyondCode\\ViewXray\\ViewXrayServiceProvider
``` 

And modify the `enabled` key in the configuration file.