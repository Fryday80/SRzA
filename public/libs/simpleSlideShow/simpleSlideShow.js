(function($) {
    var defaultOptions = {

    };

    var instances = [];


    function SimpleSlideShow(ele, settings) {
        /** @type {jQuery}  */
        this.$element = $(ele);
        this.settings = settings;
        this.interval = null;

        this.init = function() {
            this.$element.addClass(".simple-slide-show");
            $('img:first', this.$element).addClass("active");

            var clientWidth = ele.clientWidth,
                clientHeight = ele.clientHeight;

            this.$element.children("img").each(function(i, e){
                var width = $(this).width(),
                    height = $(this).height();

                if (width < height) {
                    $(this).width(clientWidth);
                } else {
                    $(this).height(clientHeight);
                }
                // this.style.position = "absolute";
                // this.style.zIndex = (i == 0)? "1": "3";
            });
            this.interval = setInterval(this.fadeToNext.bind(this), 5000);
            // this.fadeToNext();
        }
        // this.fadeToNext = function(){
        //     console.log("fade");
        //     var $active = $('img.active', this.$element);
        //     var $next = ($active.next().length > 0) ? $active.next() : $('img:first', this.$element);
        //     $next.css('z-index', 2); //move the next image up the pile
        //     $active.fadeOut(1500, function(){ //fade out the top image
        //         $active.css('z-index', 1).show()//.removeClass('active'); //reset the z-index and unhide the image
        //         $next.css('z-index', 3)//.addClass('active'); //make the next image the top one
        //     });
        // }
        this.fadeToNext = function(){
            var $active = $('.active', this.$element);
            var $next = ($active.next().length > 0) ? $active.next() : $('img:first', this.$element);
            $next.css('z-index',2);//move the next image up the pile
            $active.fadeOut(1500,function(){//fade out the top image
                $active.css('z-index',1).show().removeClass('active');//reset the z-index and unhide the image
                $next.css('z-index',3).addClass('active');//make the next image the top one
            });
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