function MapsWrapper() {
	this.template = null;
	this.callback = null;
	this.map = null;
	this.marker;
	this.search_field;
	this.lat;
	this.lng;
	this.searchbox;

	MapsWrapper.prototype.initialize = function(template, x, y, zoom) {
		this.template = template;
		var mapOptions = {
			zoom : zoom,
			center : new google.maps.LatLng(x, y),
			mapTypeId : google.maps.MapTypeId.ROADMAP
		};
		this.map = new google.maps.Map(document.getElementById(template),
				mapOptions);

		this.lat = x;
		this.lng = y;
	}
	
	MapsWrapper.prototype.initializeLocation = function(template, location) {
		this.template	= template;
		
		var geocoder = new google.maps.Geocoder();
		geocoder.geocode({'address' : location }, function(results, status) {			
			if (status == google.maps.GeocoderStatus.OK) {
				var loc = results[0].geometry.location;
				mapsWrapper.lat = loc.lat();
				mapsWrapper.lng = loc.lng();
				
				var mapOptions = {
					zoom : 12,
					position: loc,
				    center: loc,
					mapTypeId : google.maps.MapTypeId.ROADMAP
				};
				
				mapsWrapper.map = new google.maps.Map(document.getElementById(mapsWrapper.template),mapOptions);				
				mapsWrapper.setMarker(mapsWrapper.lat, mapsWrapper.lng);
			}
		});
	}

	MapsWrapper.prototype.setMarker = function(lat, lng) {
		if (this.marker != null) {
			this.marker.setMap(null);
		}

		this.marker = new google.maps.Marker({
			position : new google.maps.LatLng(lat, lng),
			map : this.map
		});

		google.maps.event.addListener(this.marker,"click",function(mark) {
			marker	= mapsWrapper.getMarker();
			map		= mapsWrapper.getMap();
			
			infowindow = new google.maps.InfoWindow();
			
			infowindow.setContent("test data");
			infowindow.setPosition(marker.latLng);
			infowindow.open(map);
		});

		var latLng = this.marker.getPosition();
		this.map.setCenter(latLng);
	}
	
	MapsWrapper.prototype.getMap	= function(){
		return this.map;
	}
	
	MapsWrapper.prototype.getMarker	= function(){
		return this.marker;
	}

	MapsWrapper.prototype.getCallback = function() {
		return this.callback;
	}
	
	MapsWrapper.prototype.setMouseListener = function(callback) {
		this.callback = callback;
		google.maps.event.addListener(this.map, "rightclick", function(event) {
			var lat = event.latLng.lat();
			var lng = event.latLng.lng();

			callback = mapsWrapper.getCallback();
			eval(callback + "(" + lat + "," + lng + ")");

			mapsWrapper.setMarker(lat, lng);
		});
	}

	MapsWrapper.prototype.searchWord = function(word, search_field) {
		this.search_field = search_field;
		var _this = this;

		var geocoder = new google.maps.Geocoder();
		geocoder.geocode({
			'address' : word
		}, _this.searchCallback);
	}

	MapsWrapper.prototype.searchCallback = function(results, status) {
		if (status == google.maps.GeocoderStatus.OK) {
			var loc = results[0].geometry.location;

			lat = loc.lat();
			lng = loc.lng();
			callback = mapsWrapper.getCallback();
			eval(callback + "(" + lat + "," + lng + ")");

			mapsWrapper.setMarker(lat, lng);
		}
	}
	
	MapsWrapper.prototype.displayLocation = function(template,address){
		var _this = this;
		this.template = template;

		var geocoder = new google.maps.Geocoder();
		geocoder.geocode({
			'address' : address
		}, _this.displayLocationCallback);
	}
	
	MapsWrapper.prototype.displayLocationCallback = function(results, status) {
		if (status == google.maps.GeocoderStatus.OK) {
			var loc = results[0].geometry.location;

			lat = loc.lat();
			lng = loc.lng();
			template = mapsWrapper.template;
			
			mapsWrapper.initialize(template,lat,lng,12);
			mapsWrapper.setMarker(lat, lng);
		}
	}
}

var mapsWrapper = new MapsWrapper();