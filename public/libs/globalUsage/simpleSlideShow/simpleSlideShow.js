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
    function SimpleSlideShow(ele, settings) {
        let self = this,
            clientWidth,
            clientHeight;
        /** @type {jQuery}  */
        this.$element = $(ele);
        this.settings = settings;
        this.interval = null;
        this.init = function() {
            this.$element.addClass("simple-slide-show");
            $('img:first', this.$element).addClass("active").show();

            clientWidth = ele.clientWidth;
            clientHeight = ele.clientHeight;
            // clientWidth = this.$element.parent().width();

            this.$element.children("img").each(function(i){
                if (this.complete) {
                    self.fitImageSize(this);
                } else {
                    $(this).on('load', function() {
                        self.fitImageSize(this);
                    });
                }
            });
            $(window).resize(this.resize.bind(this));
                        
            this.interval = setInterval(this.fadeToNext.bind(this), this.settings.intervalTime);
            this.fadeToNext();
        }
        this.resize = function() {
            $('img', this.$element).each(function(key, value) {
                clientWidth = ele.clientWidth;
                clientHeight = ele.clientHeight;
                this.fitImageSize(value);
            }.bind(this));
        }
        this.fitImageSize = function(ele) {
            var width = ele.width,
                height = ele.height;

            if( width > height ) {
                $(ele).height(clientHeight * this.settings.sizeMultiplier);
            } else {
                $(ele).width(clientWidth * this.settings.sizeMultiplier);
            }
            // if (width < height) {
            //     $(ele).width(clientWidth);
            // } else {
            //     $(ele).height(clientHeight);
            // }
        };
        this.getActive = function() {
            return $('.active', this.$element);
        };
        this.getNext = function() {
            $active = this.getActive();
            return ($active.next("img").length > 0) ? $active.next() : $('img:first', this.$element);
        };
        this.getLast = function() {

        };
        this.fadeToNext = function(){
            let $active = this.getActive(),
                $next = this.getNext();

            if (this.settings.randomRotation) {
                let angle = Math.round(Math.random() * this.settings.maxRotationAngle - (this.settings.maxRotationAngle / 2) );
                $next.css("transform", "rotate(" + angle + "deg)");
            }
            $next.css('z-index',4);
            if (this.settings.crossFade) {
                $active.fadeOut(self.settings.fadeOutTime, function () {
                    $active.css('z-index',1).removeClass('active');
                    $next.css('z-index',3).addClass('active');
                });
                $next.fadeIn(this.settings.fadeInTime, function () {
                });
            } else {
                $next.fadeIn(this.settings.fadeInTime, function () {
                    $active.removeClass('active');
                    $next.addClass('active');
                    $active.fadeOut(self.settings.fadeOutTime, function () {
                        $active.css('z-index', 1);
                        $next.css('z-index', 3);
                    });
                });
            }
        }
    }

    $.fn.simpleSlideShow = function(options) {
        //handle options
        let settings = $.extend(defaultOptions, options);
        return this.each(function() {
            let instance = new SimpleSlideShow(this, settings);
            instance.init();
            $(this).data(instance);
        });
    };
}( jQuery ));