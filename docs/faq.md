---
title: VueJS support
order: 10
---

# FAQ

To make this package work in combination with VueJS you need to enable comments in your VueJS app. 

This package works by adding specific HTML comments into the rendered views and uses
these comments to find the bounding boxes of the rendered HTML. VueJS by default removes HTML comments prior to rendering.

You can enable HTML comments in your VueJS app by setting `comments` to `true`:

```javascript
let app = new Vue({
    el: '#content',
    comments: true
});
```