(function (factory) {
  'use strict';
  if (typeof define === 'function' && define.amd) {
    define([], factory);
  } else if (typeof exports !== 'undefined') {
    module.exports = factory();
  } else {
    factory();
  }
  
}(function () {
  'use strict';
  
  var BundleLoader = function () {
  };
  
  BundleLoader.prototype.load = function (bundles, callback) {
    var self = this,
      args = [],
      countDown = bundles.length;
    
    bundles.forEach(function (bundleLoad, i) {
      bundleLoad(function (Bundle) {
        if (Bundle && Bundle.__esModule && Bundle.default) {
          Bundle = Bundle.default;
        }
        
        args[i] = Bundle;
        countDown--;
        
        if (0 === countDown && callback) {
          callback.apply(null, args);
        }
      });
    });
  };
  
  return new BundleLoader();
}));
