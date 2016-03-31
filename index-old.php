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
</head>
<body>
<?php
	require_once('menu.php');
?>	<div class="row" style="padding-top: 10px;">
		<div class="large-12 columns">
		<?php
			$query = "SELECT * FROM users ORDER BY imagineTweetCount DESC";
			$res = $mi->query($query);
		?>
			<ul class="large-block-grid-20">
		<?php
			while ($row = $res->fetch_assoc()) {
				$screen_name = $row['screen_name'];
				$profile_image_url = $row['profile_image_url'];
				$url = $row['url'];
				$imagineTweetCount = $row['imagineTweetCount'];
				$titleString = "@$screen_name - $imagineTweetCount tweets";
				echo "<li><a href='https://twitter.com/" . $screen_name . "'><img width='48' height='48' src='$profile_image_url' alt='$titleString' title='$titleString'/></a></li>\n";
			}
		?>
			</ul>
		</div>
	</div>

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
