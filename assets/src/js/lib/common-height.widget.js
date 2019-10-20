define(['jquery', 'lib-widget'],
function($,        Widget) {
    var CommonHeightWidget = function () {
        this.groups = {};
        Widget.apply(this, arguments);
    };

    CommonHeightWidget.prototype = Object.create(Widget.prototype);
    CommonHeightWidget.prototype.constructor = CommonHeightWidget;

    CommonHeightWidget.prototype._init = function () {
        var self = this;

        self.options = $.extend(
            {
                mobWidth: 768,
                isDisabled: false
            },
            self.options
        );

        self.$element.find('.js-common-height').each(function (i, element) {
            var $element = $(element),
                group = $element.attr('data-group');

            if (!group) {
                group = 'undefined'
            }
            if (!(group in self.groups)) {
                self.groups[group] = [];
            }

            self.groups[group].push($element);
        });

        setTimeout(function () { self._onResize(); }, 1000);
        setTimeout(function () { self._onResize(); }, 2000);
        setTimeout(function () { self._onResize(); }, 3000);
        setTimeout(function () { self._onResize(); }, 5000);
        setTimeout(function () { self._onResize(); }, 10000);
        $(window).resize(function () {
            self._onResize();
        });
    };

    CommonHeightWidget.prototype._onResize = function () {
        var self = this,
            maxHeight = 0,
            height = 0,
            isMobView = $(window).width() <= self.options.mobWidth;

        // optimization
        if (isMobView && self.options.isDisabled) {
            return;
        }

        console.log(self.groups);

        $.each(self.groups, function (group, elements) {
            maxHeight = 0;
            height = 0;

            $.each(elements, function (i, $element) {
                $element.height('auto');
                height = $element.outerHeight();
                if (height > maxHeight) {
                    maxHeight = height;
                }
            });
            $.each(elements, function (i, $element) {
                if (!isMobView) {
                    $element.height(maxHeight);
                }
            });

            // optimization
            self.options.isDisabled = isMobView;
        });
    };

    return CommonHeightWidget;
});
