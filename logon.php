<!doctype="html">
<html>
	<head>
		<!-- openmap START-->
		<link rel="stylesheet" href="css/default-stylesheet.css" type="text/css" >
		<script src="OpenLayers.js"></script>
		<script src="//ajax.googleapis.com/ajax/libs/jquery/1.8.3/jquery.min.js"></script>
		<script>
		  function initServerMap() {

			   
			  
			 // The overlay layer for our marker, with a simple diamond as symbol
				var overlay = new OpenLayers.Layer.Vector('Overlay', {
					styleMap: new OpenLayers.StyleMap({
						externalGraphic: 'images/server-map-icon.png',
						graphicWidth: 60, graphicHeight: 60, graphicYOffset: -24,
						title: '${tooltip}'
					})
				});

				// The location of our marker and popup. We usually think in geographic
				// coordinates ('EPSG:4326'), but the map is projected ('EPSG:3857').
				var myLocation = new OpenLayers.Geometry.Point(11.811028, 55.348785)
					.transform('EPSG:4326', 'EPSG:3857');

				// We add the marker with a tooltip text to the overlay
				overlay.addFeatures([
					new OpenLayers.Feature.Vector(myLocation, {tooltip: 'OpenLayers'})
				]);

				// A popup with some information about our location
				//var popup = new OpenLayers.Popup.FramedCloud("Popup", 
				//	myLocation.getBounds().getCenterLonLat(), null,
				//	'<div class="marker"><a target="_blank" href="?server=channelpoint.dk">Log-on domain: channelpoint.dk</a></div>', null,
				//	false // <-- true if we want a close (X) button, false otherwise
				//);

				// Finally we create the map
				map = new OpenLayers.Map({
					div: "servermap", projection: "EPSG:3857",
					layers: [new OpenLayers.Layer.OSM(), overlay],
					center: myLocation.getBounds().getCenterLonLat(), zoom: 4
				});

				 var markers = new OpenLayers.Layer.Markers( "Markers" );
				    map.addLayer(markers);

				    var size = new OpenLayers.Size(21,25);
				    var offset = new OpenLayers.Pixel(-(size.w/2), -size.h);
				    var icon = new OpenLayers.Icon('http://www.openlayers.org/dev/img/marker.png', size, offset);
				    markers.addMarker(new OpenLayers.Marker(new OpenLayers.LonLat(20,20),icon));
				    markers.addMarker(new OpenLayers.Marker(new OpenLayers.LonLat(20,0),icon.clone()));
				// and add the popup to it.
				//map.addPopup(popup);
		  }
		</script>
		<!-- openmap END-->
		<link rel="shortcut icon" href="images/icon.png" />
		<link href='http://fonts.googleapis.com/css?family=Ubuntu&subset=cyrillic,latin' rel='stylesheet' type='text/css' />
		<style>
		</style>
	</head>
	<body onload="initServerMap();">
		<div id="wapper">
			<div id="top">
				<div id="top-left">GroupPoint</div>	
				<div id="top-rigth"></div>	
			</div>
			<div id="content">
				<div id="content-topbox">
				<ul>
					<li>
						<label>E-mail</label>
						<input type="email" class="email" value="terkel@gmail.com"/>
					</li>
					<li>
						<label>Password</label>
						<input type="password" class="passwd" value="1234"/>
					</li>
					<li>
						<input type="button" class="btnLogOn" value="Logon"/>
					</li>
					<li>
						<div class="token"></div>
					</li>
				</ul>
				<script type="text/javascript">
				var getLastSelectedTagByUserId = function(userid){
					$.getJSON('restservices.php', { 
						Tags: 'getLastSelectedTagByUserId',
						userId: userid
					}, function(user) {
						//alert(user[0].lastSelectedTagId);
						$.getJSON('restservices.php', { 
							Tags: 'getTagById',
							id: user[0].lastSelectedTagId
						}, function(tag) {
							alert('lat:'+tag[0].lat+', lng: '+tag[0].lng);
						});
					});
				};

				$('.btnLogOn').on('click', function(event) {
					$.getJSON('restservices.php', { 
						Users: 'logOn',
						email: 'terkel@gmail.com',
						passwd: $('.passwd').val()
					}, function(user) {
						alert('User->token: '+user[0].token);
						getLastSelectedTagByUserId(user[0].id);
					});
				});
				</script>
				</div>
				<div id="servermap" style="width:100%; heigth:100%;"></div>
			</div>
		</div>		
	</body>
</html>
