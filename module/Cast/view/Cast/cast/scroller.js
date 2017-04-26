/**
 * Created by salt on 25.04.2017.
 */
(function() {
    "use strict";
    var isDragging = false,
        zoom = 1,
        $boxContent = $('#fullCastScroller boxcontent'),
        $dragable = $('.draggable', $boxContent);


    function onMouseDown(e) {
        isDragging = true;
    }
    function onMouseUp(e) {
        isDragging = false;
    }
    function onMouseMove(e) {
        if (!isDragging) return;
        updatePos(e.originalEvent.movementX/zoom, e.originalEvent.movementY/zoom);
        e.preventDefault();
    }
    function onMouseWheel(e) {
        let oldZoom = zoom,
            mousex = e.offsetX,
            mousey = e.offsetY,
            scrollOff = {
                x: parseInt($dragable.css('left')),
                y: parseInt($dragable.css('top'))
            };

        console.log(scrollOff);
        if (e.originalEvent.deltaY < 0) {
            zoom = ((zoom += 0.1) > 1.2)? 1.2: zoom;
            // scrollOff.x += width * 0.1;
            // scrollOff.y += height * 0.1;
        } else {
            zoom = ((zoom -= 0.1) < 0.4)? 0.4: zoom;
            // scrollOff.x -= width * 0.1;
            // scrollOff.y -= height * 0.1;
        }
        console.log(e);
        var scale = 1;
        scrollOff.x -= mousex/(scale*zoom) - mousex/scale;
        scrollOff.y -= mousey/(scale*zoom) - mousey/scale;
        //calc offset relative to mouse
        //pos -+ (width * 0.1)

        console.log(scrollOff);
        // $dragable.css('transform', 'scale('+zoom+','+zoom+')');
        $dragable.css('zoom', zoom);

        $dragable.css('left', scrollOff.x + 'px');
        $dragable.css('top', scrollOff.y + 'px');
        return false;
    }
    function updatePos(x, y) {
        let X = parseInt($dragable.css('left')) + x,
            Y = parseInt($dragable.css('top')) + y;

        $dragable.css('left', X + 'px');
        $dragable.css('top', Y + 'px');
    }
    // $boxContent.on('mousedown', onMouseDown);
    // $(window).on('mouseup', onMouseUp);
    // $(window).on('mousemove', onMouseMove);
    // $boxContent.on('wheel', onMouseWheel);


    $('.tree').workspace({

    });
})();
