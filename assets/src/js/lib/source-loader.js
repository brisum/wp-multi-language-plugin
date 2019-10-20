(function(factory) {
    'use strict';
    if (typeof define === 'function' && define.amd) {
        define(['jquery'], factory);
    } else if (typeof exports !== 'undefined') {
        module.exports = factory(require('jquery'));
    } else {
        factory(jQuery);
    }

}(function($) {
    'use strict';

    var loadedSources = {
            js: [],
            css: []
        };

    var SourceLoader = function () {};
    SourceLoader.prototype.loadScript = function(url, async, callback){
        if (-1 !== loadedSources.js.indexOf(url)) {
            if (callback) {
                callback();
            }
            return;
        }

        var script = document.createElement("script");

        if (callback) {
            if (script.readyState){  //IE
                script.onreadystatechange = function(){
                    if (script.readyState == "loaded"
                        || script.readyState == "complete"
                    ){
                        script.onreadystatechange = null;
                        loadedSources.js.push(url);
                        callback();
                    }
                };
            } else {  //Others
                script.onload = function(){
                    loadedSources.js.push(url);
                    callback();
                };
            }
        }

        script.type = "text/javascript";
        script.async = async;
        script.src = url;
        document.getElementsByTagName("body")[0].appendChild(script);
    };

    SourceLoader.prototype.loadScripts = function(scripts, callback) {
        var self = this,
            progress = 0;

        scripts.forEach(function(script) {
            self.loadScript(script, false,  function () {
                if (++progress == scripts.length) {
                    callback && callback();
                }
            });
        });
    };

    SourceLoader.prototype.loadStylesheet = function(url){
        if (-1 !== loadedSources.css.indexOf(url)) {
            return;
        }
        loadedSources.css.push(url);

        var link = document.createElement('link');

        link.rel  = 'stylesheet';
        link.type = 'text/css';
        link.media = 'all';
        link.href = url;

        document.getElementsByTagName('head')[0].appendChild(link);
    };

    SourceLoader.prototype.loadStylesheets = function(styles) {
        var self = this,
            progress = 0;

        styles.forEach(function(style) {
            self.loadStylesheet(style);
        });
    };

    return new SourceLoader();
}));
