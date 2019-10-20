class Widget {
    /**
     * @constructor
     */
    constructor($element, options) {
        this.$element = $element;
        this.options = Object.assign({}, Widget.defaults, this.$element.data(), options);
    }

    _init() {}
}
Widget.defaults = {};

module.exports = Widget;
