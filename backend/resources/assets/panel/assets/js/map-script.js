
$(function () {

    $("#geocomplete").geocomplete({
        map: ".map_canvas",
        details: "form ",
        markerOptions: {
            draggable: true
        }
    });

    if ($("input[name=latitude]").val() != "" && $("input[name=longitude]").val() != "") {
        var lat_and_long = $("input[name=latitude]").val() + "," + $("input[name=longitude]").val();
        $("#geocomplete").geocomplete("find", lat_and_long);

    }
    else {
        $("#geocomplete").val("Florida, USA");
    }

    $("#geocomplete").bind("geocode:dragged", function (event, latLng) {
        $("input[name=latitude]").val(latLng.lat());
        $("input[name=longitude]").val(latLng.lng());
        $("#reset").show();
    });

    $("#geocomplete").bind("geocode:result", function (event, result) {
        $("input[name=latitude]").val(result.geometry.location.lat());
        $("input[name=longitude]").val(result.geometry.location.lng());
    });


    $("#reset").click(function () {
        $("#geocomplete").geocomplete("resetMarker");
        $("#reset").hide();
        return false;
    });

    $("#find").click(function () {
        $("#geocomplete").trigger("geocode");
    }).click();

});


