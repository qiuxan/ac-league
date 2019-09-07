var Location = {
};

Location.getLocation = function () {
    if (navigator.geolocation) {
        navigator.geolocation.getCurrentPosition(Location.setPosition);
    }
}

Location.setPosition = function (position) {
    $.post("/setPosition", {lat: position.coords.latitude, lng: position.coords.longitude, _token: $('[name="_token"]').val()}, function () {
    });
}

$( document ).ready( function()
{
    Location.getLocation();
});