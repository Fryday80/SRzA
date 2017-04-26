(function($) {
    /**
     * @typedef {{
     *      intervalTime: number,
     *      fadeInTime: number,
     *      fadeOutTime: number,
     *      sizeMultiplier: number,
     *      randomRotation: bool,
     *      maxRotationAngle: number,
     *      crossFade: bool,
     * }} SimpleSettings
     */

    /** @var SimpleSettings */
    var defaultOptions = {
        intervalTime: 8000,
        fadeInTime: 2000,
        fadeOutTime: 1500,
        sizeMultiplier: 0.9,
        randomRotation: true,
        maxRotationAngle: 10,
        crossFade: false,
    };

    /**
     * @param ele
     * @param {SimpleSettings} settings
     * @constructor
     */
    class Workspace {
        constructor(ele, settings) {
            this.$workspace;
            this.$draggable;
            this.$content = $(ele);
            this.settings = settings;
            this.isDragging = false;
        }
        init() {
            //build wrapper
            this.$ele = this.$content.wrap('<workspace></workspace>');
            this.$ele = this.$content.wrap('<draggable></draggable>');
            this.$workspace = this.$ele.parent('workspace');
            this.$draggable = this.$ele.parent('draggable');
            //event handler
            this.$workspace.on('mousedown', this.onMouseDown.bind(this));
            $(window).on('mouseup', this.onMouseUp.bind(this));
            $(window).on('mousemove', this.onMouseMove.bind(this));
            this.$workspace.on('', this.onMouseWheel.bind(this));
        }
        onMouseDown() {
            this.isDragging = true;
        }
        onMouseUp() {
            this.isDragging = false;
        }
        onMouseMove() {
            if (!this.isDragging) return true;
            //update pos
        }
        onMouseWheel() {

        }
    }

    $.fn.workspace = function(options) {
        //handle options
        let settings = $.extend(defaultOptions, options);
        return this.each(function() {
            let instance = new Workspace(this, settings);
            instance.init();
            $(this).data(instance);
        });
    };
}( jQuery ));