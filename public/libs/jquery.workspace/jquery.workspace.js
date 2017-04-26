(function($) {
    "use strict";

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
        zoomStep: 0.1,
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
            this.canDrag = false;
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
            this.clickListeners = [];
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
            $(document).on('mousemove', this.onMouseMove.bind(this));
            this.$workspace.on('wheel', this.onMouseWheel.bind(this));
        }
        onMouseDown(e) {
            if (e.which != this.settings.dragMouseButton) return true;
            this.canDrag = true;
            this.savePos(e, this.mousePos);
            e.preventDefault();
        }
        onMouseUp(e) {
            this.canDrag = false;
            if (this.isDragging) {
                this.$draggable.removeClass('notransition');
                this.isDragging = false;
            } else {
                this.triggerClick(e);
            }
        }
        onMouseMove(e) {
            if (!this.canDrag || !this.settings.dragging) return true;
            if (!this.isDragging) {
                this.$draggable.addClass('notransition');
            }
            this.isDragging = true;
            this.savePos(e, this.mousePos);
            this.changePos(
                this.mousePos.moveX / this.zoom,
                this.mousePos.moveY / this.zoom
            );
            e.preventDefault();
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
            e.preventDefault();
        }
        changePos(x, y) {
            this.$draggable.css('left', x + parseInt(this.$draggable.css('left')));
            this.$draggable.css('top', y + parseInt(this.$draggable.css('top')));
        }
        setPos(x, y) {
            this.$draggable.css('left', x);
            this.$draggable.css('top', y);
        }
        savePos(event, to) {
            to.moveX = event.originalEvent.movementX;
            to.moveY = event.originalEvent.movementY;
            to.offsetX = event.originalEvent.offsetX;
            to.offsetY = event.originalEvent.offsetY;
            to.x = event.originalEvent.pageX - this.$workspace.offset().left;
            to.y = event.originalEvent.pageY - this.$workspace.offset().top;
        }
        goToElement(element) {
            if (this.isDragging) return false;
            let $target = $(element);
            //@todo check if target is in $draggable > .ws-content
            let x = $target.offset().left - this.$workspace.offset().left,
                y = $target.offset().top - this.$workspace.offset().top,
                dragOffX = $target.offset().left - this.$draggable.offset().left,
                dragOffY = $target.offset().top - this.$draggable.offset().top,
                centerX = this.$workspace.width() / 2,
                centerY = this.$workspace.height() / 2;

            x += centerX - x - dragOffX - $target.width() / 2;
            y += centerY - y - dragOffY - $target.height() / 2;

            this.setPos(x, y);
        }
        click(handler) {
            this.clickListeners.push(handler);
        }
        triggerClick(e) {
            for (let i = 0; i < this.clickListeners.length; i++) {
                this.clickListeners[i](e);
            }
        }
    }

    $.fn.workspace = function(options) {
        let settings = $.extend(defaultOptions, options);
        return this.each(function() {
            let instance = new Workspace(this, settings);
            instance.init();
            $(this).data('Workspace', instance);
        });
    };
    $.fn.getWorkspace = function() {
        let instance = $(this).data('Workspace');
        if (!(instance instanceof Workspace)) {
            throw new Error('This element has no Workspace');
        }
        return instance;
    };
}( jQuery ));