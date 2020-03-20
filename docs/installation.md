---
title: Installation
order: 1
---
![Example output](https://beyondco.de/github/xray/xray.png)

# Installation

You can install the package via composer:

```
composer require beyondcode/laravel-view-xray --dev
```

Please make sure that you install this package as a dev dependency, as it could expose your internal view structure to others.

The package is enabled by default - so all you need to do is visit your application in the browser and hit the Xray shortcut.

- Windows: CTRL + Shift + X
- OSX: CMD + Shift + X

This toggles the Xray view, where you can see which view (and optionally section) rendered the visual output.