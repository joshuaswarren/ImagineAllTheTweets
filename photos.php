<?php

require_once('twitter/db.php');
?>

<!DOCTYPE html>
<!--[if IE 8]> 				 <html class="no-js lt-ie9" lang="en" > <![endif]-->
<!--[if gt IE 8]><!--> <html class="no-js" lang="en" > <!--<![endif]-->

<head>
	<meta charset="utf-8" />
  <meta name="viewport" content="width=device-width" />
  <title>Magento Imagine</title>

  <link rel="stylesheet" href="css/normalize.css" />
  
  <link rel="stylesheet" href="css/foundation.css" />
  

  <script src="js/vendor/custom.modernizr.js"></script>

</head>
<body>
<?php
	require_once('menu.php');
?>
	
	<div class="row">
		<div class="large-12 columns">
		<?php
			$query = "SELECT DISTINCT imageUrl FROM tweet_urls WHERE isImage = 1";
			$res = $mi->query($query);
		?>
			<ul class="small-block-grid-2 large-block-grid-3">
		<?php
			echo "<!-- Found " . $res->num_rows . " images -->";
			while ($row = $res->fetch_assoc()) {
				$imageUrl = $row['imageUrl'];
				$query2 = "SELECT tweet_id FROM tweet_urls WHERE imageUrl = '" . $imageUrl . "'";
				$res2 = $mi->query($query2);
				$row2 = $res2->fetch_assoc();
				$tweet_id = $row2['tweet_id'];
				$query3 = "SELECT * FROM tweets WHERE tweet_id = $tweet_id";
				$res3 = $mi->query($query3);
				$row3 = $res3->fetch_assoc();
				$tweet_text = $row3['tweet_text'];
				$tweet_text = stripslashes($tweet_text); 
				// echo "<!--- $tweet_text --->";
				$screen_name = $row3['screen_name'];
				$titleString = "$screen_name: $tweet_text";
				echo "<li><a href='https://twitter.com/statuses/" . $tweet_id . "'><img src='$imageUrl' ";
				echo 'alt="' . $titleString . '"';
				echo "' title=";
				echo '"';
				echo $titleString;
				echo '"'; 
				echo "/></a></li>\n";
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
