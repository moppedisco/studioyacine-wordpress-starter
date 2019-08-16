(function(window, $) {
  var $window = $(window);
  var viewport = updateViewportDimensions();
  var timeToWaitForLast = 100;

  function init() {
    breakpoint();
  }

  function breakpoint() {
    var breakpoint;
    var breakpoint_refreshValue;
    breakpoint_refreshValue = function() {
      window.breakpoint = window
        .getComputedStyle(document.querySelector("html"), ":before")
        .getPropertyValue("content")
        .replace(/\"/g, "");
    };

    $(window)
      .resize(function() {
        breakpoint_refreshValue();
      })
      .resize();
  }

  function updateViewportDimensions() {
    var w = window,
      d = document,
      e = d.documentElement,
      g = d.getElementsByTagName("body")[0],
      x = w.innerWidth || e.clientWidth || g.clientWidth,
      y = w.innerHeight || e.clientHeight || g.clientHeight;
    return { width: x, height: y };
  }

  var waitForFinalEvent = (function() {
    var timers = {};
    return function(callback, ms, uniqueId) {
      if (!uniqueId) {
        uniqueId = "Don't call this twice without a uniqueId";
      }
      if (timers[uniqueId]) {
        clearTimeout(timers[uniqueId]);
      }
      timers[uniqueId] = setTimeout(callback, ms);
    };
  })();

  window.Application = {
    init: init
  };
})(window, jQuery); // Self execute

Application.init();
