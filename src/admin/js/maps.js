//Declaramos las variables que vamos a user
var address = null;
var lat = null;
var lng = null;
var map = null;
var geocoder = null;
var marker = null;
var markersArray = [];


function posicionar(lat,lng, map_canvas) {
	var myLatlng = new google.maps.LatLng(lat,lng);
	var mapOptions = {
		center: myLatlng,
		zoom: 15,
		mapTypeId: google.maps.MapTypeId.ROADMAP,
        disableDefaultUI: true
	};
	map = new google.maps.Map(document.getElementById(map_canvas), mapOptions);

	marker = new google.maps.Marker({
		position: myLatlng,
		map: map,
		draggable: true
	});


	google.maps.event.addListener(map, "click", function(marker, event) {
		if(marker) {
			removeOverlay();
		}
		else {
			placeMarker(event.latLng);
			document.getElementById("longitud_"+map_canvas).value=event.lng();
			document.getElementById("latitud_"+map_canvas).value=event.lat();
			document.getElementById("position_lat_long_"+map_canvas).value=event.lat()+":"+event.lng()+"@"+document.getElementById("cerca_posicio_"+map_canvas).value;
			alert(document.getElementById("longitud_"+map_canvas).value);
			alert(document.getElementById("latitud_"+map_canvas).value);
			alert(document.getElementById("position_lat_long_"+map_canvas).value);
		}
	});
}


function recalc_gmaps(map_canvas) {
	var latlng = new google.maps.LatLng(41.387917, 2.1699187)
	var myOptions = {
		zoom: 15,
		center: latlng,
		mapTypeId: google.maps.MapTypeId.ROADMAP,
        disableDefaultUI: true
	};
	map = new google.maps.Map(document.getElementById(map_canvas), myOptions);

	var cerca ="cerca_posicio_"+ map_canvas;
	address=document.getElementById(cerca).value;

	geocoder = new google.maps.Geocoder();
	geocoder.geocode( { 'address': address}, function(results, status) {
		if (status == google.maps.GeocoderStatus.OK) {
			map.setCenter(results[0].geometry.location);
			marker = new google.maps.Marker({
				map: map,
				position: results[0].geometry.location,
				draggable: true
			});
			updatePosition(results[0].geometry.location, map_canvas);
			google.maps.event.addListener(marker, 'dragend', function(){
				updatePosition(marker.getPosition(), map_canvas);
			});

	  } else {
		  alert("No podemos encontrar la dirección, error: " + status);
	  }
	});
}

function updatePosition(latLng, map_canvas) {
	document.getElementById("longitud_"+map_canvas).value=latLng.lat();
	document.getElementById("latitud_"+map_canvas).value=latLng.lng();
	document.getElementById("position_lat_long_"+map_canvas).value=latLng.lat()+":"+latLng.lng()+"@"+document.getElementById("cerca_posicio_"+map_canvas).value;
}

function placeMarker(location) {
	var marker = new google.maps.Marker({
		position: location,
		map: map
	});
	markersArray.push(marker);
	map.setCenter(location);
}

function addMarker(location) {
	marker = new google.maps.Marker({
		position: location,
		map: map
	});
	markersArray.push(marker);e
}

// Removes the overlays from the map, but keeps them in the array
function clearOverlays() {
	if (markersArray) {
		for (i in markersArray) {
			markersArray[i].setMap(null);
		}
	}
}

// Shows any overlays currently in the array
function showOverlays() {
	if (markersArray) {
		for (i in markersArray) {
			markersArray[i].setMap(map);
		}
	}
}

// Deletes all markers in the array by removing references to them
function removeOverlay() {
	if (markersArray) {
		for (i in markersArray) {
			markersArray[i].setMap(null);
		}
		markersArray.length = 0;
	}
}