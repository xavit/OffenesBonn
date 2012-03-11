<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN" "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="de" lang="de-de">
<head>
<title>Map | Testanwendung</title>

470,19

<link rel="stylesheet" href="http://code.leafletjs.com/leaflet-0.3.1/leaflet.css" />
<!--[if lte IE 8]>
    <link rel="stylesheet" href="http://code.leafletjs.com/leaflet-0.3.1/leaflet.ie.css" />
<![endif]-->
<script src="http://code.leafletjs.com/leaflet-0.3.1/leaflet.js"></script>



  </head>
  <body>
<div id="map" style="width: 600px; height: 400px"></div>

	<script src="../dist/leaflet.js"></script>
	<script>
		var map = new L.Map('map');

		var cloudmadeUrl = 'http://{s}.tile.cloudmade.com/4734f27bb2ce4e9aa390288b06204728/52932/256/{z}/{x}/{y}.png',
			cloudmadeAttribution = 'Map data &copy; 2011 OpenStreetMap contributors, Imagery &copy; 2011 CloudMade',
			cloudmade = new L.TileLayer(cloudmadeUrl, {maxZoom: 18, attribution: cloudmadeAttribution});

		map.setView(new L.LatLng(50.7064318015311,7.12659787095378), 15).addLayer(cloudmade);

		
	</script>
</body>
</html>