define(['jquery', 'lib-bundle-loader'],
function($,        BundleLoader) {
    'use strict';

    class RequireInit {
        constructor($element) {
            this.$element = $element;
            this._init();
        }

        _init() {
            let self = this,
                requires = {};

            self.$element.find('[data-require-init]').each(function (i, element) {
                let $element = $(element),
                    args = $element.attr('data-require-init').split(','),
                    name = args[0],
                    priority = undefined !== args[1] ? parseInt(args[1]) : 100;

                if (!requires[priority + '-' + name]) {
                    requires[priority + '-' + name] = {
                        elements: [],
                        name: name
                    }
                }
                requires[priority + '-' + name].elements.push($element);
            });

            Object.keys(requires).sort().forEach(function (requireKey) {
                let requireInitConfig = requires[requireKey];

                Object.keys(requireInitConfig.elements).forEach(function (elementKey) {
                    let $element = requireInitConfig.elements[elementKey],
                        requireInit = requireInitConfig.name,
                        callback = requireInit.match(/.+Widget$/) || requireInit.match(/.+\.widget$/)
                            ? (function (requireElement) {
                                if (requireElement && requireElement.__esModule && requireElement.default) {
                                    requireElement = requireElement.default;
                                }
                                let widget = new requireElement($element);
                                widget._init();
                            })
                            : (function () {});

                    console.log('data-require-init', requireInit);

                    switch(requireInit){
                        /* case template:
                        case '{{name}}':
                            BundleLoader.load([require('bundle-loader?&name={{name}}!{{name}}')], callback);
                            break;
                        */

                        
                        case 'bundle-loader':
                            BundleLoader.load([require('bundle-loader?&name=bundle-loader!bundle-loader')], callback);
                            break;
                        

                        case 'common-height.widget':
                            BundleLoader.load([require('bundle-loader?&name=common-height.widget!common-height.widget')], callback);
                            break;
                        

                        case 'require-init.dist':
                            BundleLoader.load([require('bundle-loader?&name=require-init.dist!require-init.dist')], callback);
                            break;
                        

                        case 'require-init':
                            BundleLoader.load([require('bundle-loader?&name=require-init!require-init')], callback);
                            break;
                        

                        case 'source-loader':
                            BundleLoader.load([require('bundle-loader?&name=source-loader!source-loader')], callback);
                            break;
                        

                        case 'widget':
                            BundleLoader.load([require('bundle-loader?&name=widget!widget')], callback);
                            break;
                        

                        default:
                            console.log('not found require init: ' + requireInit);
                    }
                });
            });
        }
    }

    return RequireInit;
});
