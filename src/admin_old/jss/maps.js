//Declaramos las variables que vamos a user
var address = null;
var lat = null;
var lng = null;
var map = null;
var geocoder = null;
var marker = null;
var markersArray = [];

function load() {
	var mapOptions = {
		center: new google.maps.LatLng(41.387917, 2.1699187),
		zoom: 13,
		mapTypeId: google.maps.MapTypeId.ROADMAP
	};
	var map = new google.maps.Map(document.getElementById("map_canvas"), mapOptions);

	google.maps.event.addListener(map, "click", function(event) {
		if(marker) {
			removeOverlay(event.latLng);
		}
		else {
			removeOverlay(event.latLng);
			placeMarker(event.latLng);
			document.getElementById("longitud").value=event.lng();
			document.getElementById("latitud").value=event.lat();
			document.getElementById("position_lat_long").value=event.lat()+":"+event.lng()+"@"+document.getElementById("cerca_posicio").value;
			alert(document.getElementById("longitud").value);
			alert(document.getElementById("latitud").value);
			alert(document.getElementById("position_lat_long").value);
		}
	});
}

function posicionar(lat,longi) {
	var myLatlng = new google.maps.LatLng(lat,longi);
	var mapOptions = {
		center: myLatlng,
		zoom: 15,
		mapTypeId: google.maps.MapTypeId.ROADMAP
	};
	map = new google.maps.Map(document.getElementById("map_canvas"), mapOptions);

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
			document.getElementById("longitud").value=event.lng();
			document.getElementById("latitud").value=event.lat();
			document.getElementById("position_lat_long").value=event.lat()+":"+event.lng()+"@"+document.getElementById("cerca_posicio").value;
			alert(document.getElementById("longitud").value);
			alert(document.getElementById("latitud").value);
			alert(document.getElementById("position_lat_long").value);
		}
	});
}


function recalc_gmaps() {
	var latlng = new google.maps.LatLng(41.387917, 2.1699187)
	var myOptions = {
		zoom: 15,
		center: latlng,
		mapTypeId: google.maps.MapTypeId.ROADMAP
	};
	map = new google.maps.Map(document.getElementById("map_canvas"), myOptions);

	//obtengo la direccion del formulario
	address=document.getElementById("cerca_posicio").value;

	//hago la llamada al geodecoder
	geocoder = new google.maps.Geocoder();
	geocoder.geocode( { 'address': address}, function(results, status) {
		//si el estado de la llamado es OK
		if (status == google.maps.GeocoderStatus.OK) {
			//centro el mapa en las coordenadas obtenidas
			map.setCenter(results[0].geometry.location);
			//coloco el marcador en dichas coordenadas
			marker = new google.maps.Marker({
				map: map,//el mapa creado en el paso anterior
				position: results[0].geometry.location,
				draggable: true
			});
			//actualizo el formulario
			updatePosition(results[0].geometry.location);

			//Añado un listener para cuando el markador se termine de arrastrar
			//actualize el formulario con las nuevas coordenadas
			google.maps.event.addListener(marker, 'dragend', function(){
				updatePosition(marker.getPosition());
			});

	  } else {
		  //si no es OK devuelvo error
		  alert("No podemos encontrar la dirección, error: " + status);
	  }
	});
}

function updatePosition(latLng) {
	document.getElementById("longitud").value=latLng.lat();
	document.getElementById("latitud").value=latLng.lng();
	document.getElementById("position_lat_long").value=latLng.lat()+":"+latLng.lng()+"@"+document.getElementById("cerca_posicio").value;
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
	markersArray.push(marker);
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