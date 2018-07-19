var hasProp = {}.hasOwnProperty,
  bind = function(fn, me){ return function(){ return fn.apply(me, arguments); }; };

(function() {
  var $, MAX_ZINDEX, util;
  window.Xray = {};
  if (!($ = window.jQuery)) {
    return;
  }
  MAX_ZINDEX = 2147483647;
  Xray.init = (function() {
    var is_mac;
    if (Xray.initialized) {
      return;
    }
    Xray.initialized = true;
    is_mac = navigator.platform.toUpperCase().indexOf('MAC') !== -1;
    $(document).keydown(function(e) {
      if ((is_mac && e.metaKey || !is_mac && e.ctrlKey) && e.shiftKey && e.keyCode === 88) {
        if (Xray.isShowing) {
          Xray.hide();
        } else {
          Xray.show();
        }
      }
      if (Xray.isShowing && e.keyCode === 27) {
        return Xray.hide();
      }
    });
    return $(function() {
      new Xray.Overlay;
      Xray.findTemplates();
      return typeof console !== "undefined" && console !== null ? console.log("Ready to Xray. Press " + (is_mac ? 'cmd+shift+x' : 'ctrl+shift+x') + " to scan your UI.") : void 0;
    });
  })();
  Xray.specimens = function() {
    return Xray.Specimen.all;
  };
  Xray.constructorInfo = function(constructor) {
    var func, info, ref;
    if (window.XrayPaths) {
      ref = window.XrayPaths;
      for (info in ref) {
        if (!hasProp.call(ref, info)) continue;
        func = ref[info];
        if (func === constructor) {
          return JSON.parse(info);
        }
      }
    }
    return null;
  };
  Xray.findTemplates = function() {
    return util.bm('findTemplates', function() {
      var $templateContents, _, comment, comments, el, i, id, len, name, path, ref, results;
      comments = $('*:not(iframe,script)').contents().filter(function() {
        return this.nodeType === 8 && this.data.slice(0, 10) === "XRAY START";
      });
      results = [];
      for (i = 0, len = comments.length; i < len; i++) {
        comment = comments[i];
        ref = comment.data.match(/^XRAY START (\d+) (.*?) (.*?)$/), _ = ref[0], id = ref[1], name = ref[2], path = ref[3];
        $templateContents = new jQuery;
        el = comment.nextSibling;
        while (!(!el || (el.nodeType === 8 && el.data === ("XRAY END " + id)))) {
          if (el.nodeType === 1 && el.tagName !== 'SCRIPT') {
            $templateContents.push(el);
          }
          el = el.nextSibling;
        }
        if ((el != null ? el.nodeType : void 0) === 8) {
          el.parentNode.removeChild(el);
        }
        comment.parentNode.removeChild(comment);
        results.push(Xray.Specimen.add($templateContents, {
          name: name,
          path: path
        }));
      }
      return results;
    });
  };
  Xray.show = function() {
    return Xray.Overlay.instance().show();
  };
  Xray.hide = function() {
    return Xray.Overlay.instance().hide();
  };
  Xray.Specimen = (function() {
    Specimen.all = [];

    Specimen.add = function(el, info) {
      if (info == null) {
        info = {};
      }
      return this.all.push(new this(el, info));
    };

    Specimen.remove = function(el) {
      var ref;
      return (ref = this.find(el)) != null ? ref.remove() : void 0;
    };

    Specimen.find = function(el) {
      var i, len, ref, specimen;
      if (el instanceof jQuery) {
        el = el[0];
      }
      ref = this.all;
      for (i = 0, len = ref.length; i < len; i++) {
        specimen = ref[i];
        if (specimen.el === el) {
          return specimen;
        }
      }
      return null;
    };

    Specimen.reset = function() {
      return this.all = [];
    };

    function Specimen(contents, info) {
      if (info == null) {
        info = {};
      }
      this.makeLabel = bind(this.makeLabel, this);
      this.el = contents instanceof jQuery ? contents[0] : contents;
      this.$contents = $(contents);
      this.name = info.name;
      this.path = info.path;
    }

    Specimen.prototype.remove = function() {
      var idx;
      idx = this.constructor.all.indexOf(this);
      if (idx !== -1) {
        return this.constructor.all.splice(idx, 1);
      }
    };

    Specimen.prototype.isVisible = function() {
      return this.$contents.length && this.$contents.is(':visible');
    };

    Specimen.prototype.makeBox = function() {
      this.bounds = util.computeBoundingBox(this.$contents);
      this.$box = $("<div class='xray-specimen " + this.constructor.name + "'>").css(this.bounds).attr('title', this.path);
      if (this.$contents.css('position') === 'fixed') {
        this.$box.css({
          position: 'fixed',
          top: this.$contents.css('top'),
          left: this.$contents.css('left')
        });
      }
      return this.$box.append(this.makeLabel);
    };

    Specimen.prototype.makeLabel = function() {
      return $("<div class='xray-specimen-handle " + this.constructor.name + "'>").append(this.name);
    };

    return Specimen;

  })();
  Xray.Overlay = (function() {
    Overlay.instance = function() {
      return this.singletonInstance || (this.singletonInstance = new this);
    };

    function Overlay() {
      Xray.Overlay.singletonInstance = this;
      this.bar = new Xray.Bar('#xray-bar');
      this.shownBoxes = [];
      this.$overlay = $('<div id="xray-overlay">');
      this.$overlay.click((function(_this) {
        return function() {
          return _this.hide();
        };
      })(this));
    }

    Overlay.prototype.show = function() {
      this.reset();
      Xray.isShowing = true;
      return util.bm('show', (function(_this) {
        return function() {
          var element, i, len, results, specimens;
          _this.bar.$el().find('#xray-bar-togglers .xray-bar-btn').removeClass('active');
          if (!_this.$overlay.is(':visible')) {
            $('body').append(_this.$overlay);
            _this.bar.show();
            Xray.findTemplates();
            specimens = Xray.specimens();
            _this.bar.$el().find('.xray-bar-all-toggler').addClass('active');
            results = [];
            for (i = 0, len = specimens.length; i < len; i++) {
              element = specimens[i];
              if (!element.isVisible()) {
                continue;
              }
              element.makeBox();
              element.$box.css({
                zIndex: Math.ceil(MAX_ZINDEX * 0.9 + element.bounds.top + element.bounds.left)
              });
              _this.shownBoxes.push(element.$box);
              results.push($('body').append(element.$box));
            }
            return results;
          }
        };
      })(this));
    };

    Overlay.prototype.reset = function() {
      var $box, i, len, ref;
      ref = this.shownBoxes;
      for (i = 0, len = ref.length; i < len; i++) {
        $box = ref[i];
        $box.remove();
      }
      return this.shownBoxes = [];
    };

    Overlay.prototype.hide = function() {
      Xray.isShowing = false;
      this.$overlay.detach();
      this.reset();
      return this.bar.hide();
    };

    return Overlay;

  })();
  Xray.Bar = (function() {
    function Bar(el) {
      this.el = el;
    }

    Bar.prototype.$el = function() {
      if ((this.$el_memo != null) && $.contains(window.document, this.$el_memo[0])) {
        return this.$el_memo;
      }
      this.$el_memo = $(this.el);
      this.$el_memo.css({
        zIndex: MAX_ZINDEX
      });
      this.$el_memo.find('.xray-bar-all-toggler').click(function() {
        return Xray.show();
      });
      return this.$el_memo;
    };

    Bar.prototype.show = function() {
      this.$el().show();
      this.originalPadding = parseInt($('html').css('padding-bottom'));
      if (this.originalPadding < 40) {
        return $('html').css({
          paddingBottom: 40
        });
      }
    };

    Bar.prototype.hide = function() {
      this.$el().hide();
      return $('html').css({
        paddingBottom: this.originalPadding
      });
    };

    return Bar;

  })();
  return util = {
    bm: function(name, fn) {
      var result, time;
      time = new Date;
      result = fn();
      return result;
    },
    computeBoundingBox: function($contents) {
      var $el, boxFrame, el, frame, i, len;
      if ($contents.length === 1 && $contents.height() <= 0) {
        return util.computeBoundingBox($contents.children());
      }
      boxFrame = {
        top: Number.POSITIVE_INFINITY,
        left: Number.POSITIVE_INFINITY,
        right: Number.NEGATIVE_INFINITY,
        bottom: Number.NEGATIVE_INFINITY
      };
      for (i = 0, len = $contents.length; i < len; i++) {
        el = $contents[i];
        $el = $(el);
        if (!$el.is(':visible')) {
          continue;
        }
        frame = $el.offset();
        frame.right = frame.left + $el.outerWidth();
        frame.bottom = frame.top + $el.outerHeight();
        if (frame.top < boxFrame.top) {
          boxFrame.top = frame.top;
        }
        if (frame.left < boxFrame.left) {
          boxFrame.left = frame.left;
        }
        if (frame.right > boxFrame.right) {
          boxFrame.right = frame.right;
        }
        if (frame.bottom > boxFrame.bottom) {
          boxFrame.bottom = frame.bottom;
        }
      }
      return {
        left: boxFrame.left,
        top: boxFrame.top,
        width: boxFrame.right - boxFrame.left,
        height: boxFrame.bottom - boxFrame.top
      };
    }
  };
})();

// ---
// generated by coffee-script 1.9.2