(function($) {
    /**
     * @typedef {{
     *      zoom: bool,
     *      zoomStep: number,
     *      minZoom: number,
     *      maxZoom: number,
     *      dragging: bool,
     *      dragMouseButton: number,
     * }} SimpleSettings
     */

    /** @var SimpleSettings */
    var defaultOptions = {
        zoom: false,
        zoomStep: 0.08,
        minZoom: 0.5,
        maxZoom: 1.5,
        dragging: true,
        dragMouseButton: 1,
    };

    /**
     * @param ele
     * @param {SimpleSettings} settings
     * @constructor
     */
    class Workspace {
        constructor(ele, settings) {
            this.$ele = ele;
            this.$workspace;
            this.$draggable;
            this.$content = $(ele);
            this.settings = settings;
            this.isDragging = false;
            this.mousePos = {
                moveX:      0,
                moveY:      0,
                offsetX:    0,
                offsetY:    0,
                x:          0,
                y:          0,
            };
            this.zoom = 1;
        }
        init() {
            if ($(this.$ele).is('workspace')) {
                //build inner
                this.$ele = $(this.$ele);
                if (!this.$ele.has('draggable').length) {
                    //create draggable
                    this.$ele.append('<draggable></draggable>');
                }
                this.$draggable = this.$ele.children('draggable');
                this.$workspace = this.$ele;

                let childCount = this.$draggable.children().length;
                if (childCount == 0) {
                    this.$draggable.append('<div></div>');
                } else if (childCount > 1) {
                    throw new Error('jquery.workspace max 1 element is allowed in the draggable element!');//[, fileName[, lineNumber]]])
                }

                this.$ele = this.$draggable.children().first();
            } else {
                //build wrapper
                this.$ele = this.$content.wrap('<workspace></workspace>');
                this.$ele = this.$content.wrap('<draggable></draggable>');
                this.$draggable = this.$ele.parent('draggable');
                this.$workspace = this.$draggable.parent('workspace');
            }
            this.$ele.addClass('ws-content');
            //event handler
            this.$workspace.on('mousedown', this.onMouseDown.bind(this));
            $(window).on('mouseup', this.onMouseUp.bind(this));
            $(window).on('mousemove', this.onMouseMove.bind(this));
            this.$workspace.on('wheel', this.onMouseWheel.bind(this));
        }
        onMouseDown(e) {
            if (e.which != this.settings.dragMouseButton) return true;
            this.isDragging = true;
            this.savePos(e, this.mousePos);
        }
        onMouseUp() {
            this.isDragging = false;
        }
        onMouseMove(e) {
            if (!this.isDragging || !this.settings.dragging) return true;

            this.savePos(e, this.mousePos);
            this.changePos(
                this.mousePos.moveX / this.zoom,
                this.mousePos.moveY / this.zoom
            );
        }
        onMouseWheel(e) {
            if (!this.settings.zoom) return true;

            let zoomChange = 0;
            this.zoom += zoomChange = (e.originalEvent.deltaY > 0)? -this.settings.zoomStep: this.settings.zoomStep;
            this.zoom = Math.max(this.settings.minZoom, Math.min(this.settings.maxZoom, this.zoom));
            this.savePos(e, this.mousePos);
            this.$draggable.css('zoom', this.zoom);
            this.changePos(
                (this.mousePos.offsetX * zoomChange) * -1,
                (this.mousePos.offsetY * zoomChange) * -1
            );

        }
        changePos(x, y) {
            this.$draggable.css('left', x + parseInt(this.$draggable.css('left')));
            this.$draggable.css('top', y + parseInt(this.$draggable.css('top')));
        }

        savePos(event, to) {
            to.moveX = event.originalEvent.movementX;
            to.moveY = event.originalEvent.movementY;
            to.offsetX = event.originalEvent.offsetX;
            to.offsetY = event.originalEvent.offsetY;
            to.x = event.originalEvent.pageX - this.$workspace.offset().left;
            to.y = event.originalEvent.pageY - this.$workspace.offset().top;
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