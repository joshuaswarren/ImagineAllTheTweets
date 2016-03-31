<?php

require_once('twitter/db.php');
?>

<!DOCTYPE html>
<!--[if IE 8]> 				 <html class="no-js lt-ie9" lang="en" > <![endif]-->
<!--[if gt IE 8]><!--> <html class="no-js" lang="en" > <!--<![endif]-->

<head>
	<meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Magento Imagine</title>

  <link rel="stylesheet" href="css/foundation.css" />
  <script src="js/vendor/modernizr.js"></script>
  <script src="//ajax.googleapis.com/ajax/libs/jquery/2.1.0/jquery.min.js"></script>
    <script src="js/freewall.js"></script>
    <style type="text/css">
        body {
            background: black;
        }
    </style>
      <style type="text/css">
      html, body, #map-canvas { height: 100%; margin: 0; padding: 0;}
    </style>
    <script type="text/javascript"
      src="https://maps.googleapis.com/maps/api/js?key=AIzaSyCvlzG6d3V0T3dSfXD67j6E7AUDAtUkhwY&amp;sensor=false">
    </script>
    <script type="text/javascript">
      function initialize() {
        var mapOptions = {
          center: { lat: 36.174968719, lng: -115.1372222900},
          zoom: 3
        };
        var map = new google.maps.Map(document.getElementById('map-canvas'),
            mapOptions);
<?php
			$query = "SELECT * FROM tweets WHERE geo_lat != '0.000'";
			$res = $mi->query($query);
			$count = 0; 
			while ($row = $res->fetch_assoc()) {
				$geo_lat = $row['geo_lat'];
				$geo_long = $row['geo_long'];
				echo '
				var myLatlng' . $count . ' = new google.maps.LatLng(' . $geo_lat . ',' . $geo_long . ');
				var marker' . $count . ' = new google.maps.Marker({
    position: myLatlng' . $count . ',
    map: map,
});

				';
				$count++; 
			}
		?>


            
      }
      google.maps.event.addDomListener(window, 'load', initialize);
    </script>
</head>
<body>
<?php
	require_once('menu.php');
?>
<div>
	</div>
			<div id="map-canvas"></div>

<script>
    document.write('<script src=' +
        ('__proto__' in {} ? 'js/vendor/zepto' : 'js/vendor/jquery') +
        '.js><\/script>')
</script>

  <script src="js/foundation.min.js"></script>
  <!--
  
  <script src="js/foundation/foundation.js"></script>
  
  <script src="js/foundation/foundation.alerts.js"></script>
  
  <script src="js/foundation/foundation.clearing.js"></script>
  
  <script src="js/foundation/foundation.cookie.js"></script>
  
  <script src="js/foundation/foundation.dropdown.js"></script>
  
  <script src="js/foundation/foundation.forms.js"></script>
  
  <script src="js/foundation/foundation.joyride.js"></script>
  
  <script src="js/foundation/foundation.magellan.js"></script>
  
  <script src="js/foundation/foundation.orbit.js"></script>
  
  <script src="js/foundation/foundation.placeholder.js"></script>
  
  <script src="js/foundation/foundation.reveal.js"></script>
  
  <script src="js/foundation/foundation.section.js"></script>
  
  <script src="js/foundation/foundation.tooltips.js"></script>
  
  <script src="js/foundation/foundation.topbar.js"></script>
  
  -->
  
  <script>
    $(document).foundation();
  </script>
</body>
</html>
