(function( $ ){

  $.staticMap = function( options ){

    var defaults = {
      zoom : 3,
      width: 300,//image width
      height: 300,//image height
      center: 'USA', //could be string address or lat,lon("-3.444,3.222")
      markerIcon: '', //url to custom icon
      key: '',// google map key
      sensor: false,
      mapType: 'roadmap',//map type []
      scale: 1 // helps when need map on mobile, need change scale to 2
    };

    var settings = $.extend( {}, defaults, options );

    var url = 'http://maps.googleapis.com/maps/api/staticmap?';
    if(settings.key != ''){
      url += 'key='+settings.key+'&';
    }//if
    url += 'center='+settings.center+'&';
    url += 'zoom='+settings.zoom+'&';
    url += 'size='+settings.width+'x'+settings.height+'&';
    url += 'markers=';
    if(settings.markerIcon != ''){
      url +='icon:'+settings.markerIcon+'|';
    }
    url += settings.center+'&';
    url += 'maptype='+settings.mapType+'&';
    url += 'scale='+settings.scale+'&';
    url += 'sensor='+settings.sensor;

    return url;

  }//map

  $.liveMapLink = function(apikey, options ){
    var defaults = {
        size: '500x500',
        zoom : 1,
        // width: 300,//image width
        // height: 300,//image height
        center: 'USA', //could be string address or lat,lon("-3.444,3.222")
        // markerIcon: '', //url to custom icon
        // key: '',// google map key
        // sensor: false,
        // mapType: 'roadmap',//map type []
        scale: 1 // helps when need map on mobile, need change scale to 2
    };
    var settings = $.extend( {}, defaults, options );

    var url = 'https://maps.googleapis.com/maps/api/staticmap?';
    for(var key in settings) {
        url += key + '=' + settings[key] + '&';
    }
    url += "key=" + apikey;
    // url = url.substring(0, url.length - 1);
    return url;

  }
})( jQuery );
