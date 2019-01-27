var map = null;
var markers = [];
var geolocationZoom = 11;
var geolocMakerInit = false;

$(function(){
    $(window).bind("load resize", function() {
        var topOffset = 160;
        var width = (this.window.innerWidth > 0) ? this.window.innerWidth : this.screen.width;
        if (width < 768) {
            topOffset += 50; // 2-row-menu
        }
        var height = ((this.window.innerHeight > 0) ? this.window.innerHeight : this.screen.height) - 2;
        height = height - topOffset;
        if (height < 1) height = 1;
        if (height > topOffset) {
            $("#home-map").css("min-height", (height) + "px");
        }
    });

    map = new google.maps.Map(document.getElementById("home-map"), {
        center: {lat: 46.81676808590648, lng: 2.4233221091003587},
        zoom:   6,
        disableDefaultUI:  true,
        streetViewControl: true,
        zoomControl:       true,
        rotateControl:     true,
        fullscreenControl: true
    });

    var legend = $("[data-id='map-legend']");
    map.controls[google.maps.ControlPosition.LEFT_TOP].push($("[data-id='map-legend']").get(0));
    var mapLocate = $("[data-id='map-locate']");
    var geolocationMarker = new GeolocationMarker(map, null, {visible: false});
    geolocationMarker.setCircleOptions({});
    geolocationMarker.addListener('geolocation_error', function(){
        mapLocate.attr('disabled', true);
    });

    geolocationMarker.addListener('position_changed', function(){
        if (geolocMakerInit === false) {
            map.setCenter(geolocationMarker.getPosition());
            map.setZoom(geolocationZoom);
            mapLocate.click(function(){
                map.setCenter(geolocationMarker.getPosition());
                map.setZoom(geolocationZoom);
            });
            geolocMakerInit = true;
        }
    });

    $("[data-id='place-info-close']").click(function(){
        $("[data-id='place-info']").removeClass('show');
    });

    updateMarkers();

    $("#beer-filter").change(function(){
        updateMarkers();
    });
});

/**
 * Get place list and update map markers
 */
function updateMarkers()
{
    var beer = $("#beer-filter").val();
    if (beer.length === 0) {
        beer = null;
    }

    $.each(markers, function(index, marker){
        marker.setMap(null);
    });
    markers = [];

    $.ajax({
        type: 'GET',
        url: $("#home-map").data('url'),
        dataType: 'json',
        data: {beer: beer},
        success: function(data){
            $.each(data, function(index, place){
                var marker = new google.maps.Marker({
                    icon:     place.marker,
                    position: {lat: parseFloat(place.latitude), lng: parseFloat(place.longitude)},
                    title:    place.name,
                    map:      map
                });
                marker.addListener('click', function(){
                    displayPlaceInfo(place);
                });
                markers.push(marker);
            });
        }
    });
}

/**
 * @param placeData
 */
function displayPlaceInfo(placeData)
{
    var placeInfo          = $("[data-id='place-info']");
    var placeName          = $("[data-id='place-name']", placeInfo);
    var placeAddress       = $("[data-id='place-address']", placeInfo);
    var availableBeers     = $("[data-id='place-available-beers']", placeInfo);
    var availableBeersList = $("[data-id='place-available-beers-list']", placeInfo);
    var informationLink    = $("[data-id='place-info-link']", placeInfo);
    var address            = placeData['address'];

    if (placeData.addressComplement && placeData.addressComplement.length > 0) {
        address += ' ' + placeData.addressComplement;
    }
    address += ', ' + placeData.zipCode + ' ' + placeData.city;

    placeName.text(placeData.name);
    placeAddress.text(address);

    setTextInformation($("[data-id='place-phone']", placeInfo), placeData.phone);
    setTextInformation($("[data-id='place-email']", placeInfo), placeData.email);
    setLinkInformation($("[data-id='place-website']", placeInfo), placeData.website);
    setLinkInformation($("[data-id='place-facebook']", placeInfo), placeData.facebook);

    availableBeersList.empty();
    if (placeData.beers && Object.keys(placeData.beers).length > 0) {
        $.each(placeData.beers, function(index, beer){
            var beerDiv =  $("<div class='beer'>");
            beerDiv.text(beer.name + ' (' + beer.brewery + ')');
            availableBeersList.append(beerDiv);
        });
        availableBeers.show();
    } else {
        availableBeers.hide();
    }

    if (placeData.route) {
        informationLink.attr('href', placeData.route);
        informationLink.show();
    } else {
        informationLink.hide();
        informationLink.attr('href','#');
    }

    placeInfo.addClass('show');
    placeInfo.removeClass('init');
}

/**
 * @param parent
 * @param value
 */
function setTextInformation(parent, value)
{
    var span = $('span', parent);

    if (value && value.length > 0) {
        span.text(value);
        parent.show();
    } else {
        parent.hide();
        span.text(null);
    }
}

/**
 * @param parent
 * @param value
 */
function setLinkInformation(parent, value)
{
    var link =  $('a', parent);

    if (value && value.length > 0) {
        link.text(value);
        link.attr('href', value);
        parent.show();
    } else {
        parent.hide();
        link.text(null);
        link.attr('href', '#');
    }
}