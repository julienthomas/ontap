$(function(){
    var mapDiv = $("#place-map");
    var center = {lat: parseFloat(mapDiv.data('lat')), lng: parseFloat(mapDiv.data('lng'))};

    var map = new google.maps.Map(mapDiv.get(0), {
        center: center,
        zoom:   16
    });

    new google.maps.Marker({
        icon: mapDiv.data('marker'),
        position: center,
        map: map
    });
});