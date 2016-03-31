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
    <script src="js/foundation/foundation.js"></script>
    <script src="js/foundation/foundation.topbar.js"></script>
</head>
<body>
<?php
	require_once('menu.php');
?>
	<div class="row" style="padding-top: 10px;">
		<div class="large-12 columns">
		<h1 class="row">#ImagineCommerce Twitter Leaderboard</h1>
		<div class="row">
		Users Tweeting the Most About Imagine:
		</div>
		<div class="row" style="padding-top: 10px;">&nbsp;</div>
		<?php
			$query = "SELECT * FROM users ORDER BY imagineTweetCount DESC LIMIT 5";
			$res = $mi->query($query);
		?>
		<?php
			while ($row = $res->fetch_assoc()) {
				$screen_name = $row['screen_name'];
				$name = $row['name'];
				$profile_image_url = $row['profile_image_url'];
				$url = $row['url'];
				$description = $row['description'];
				$imagineTweetCount = $row['imagineTweetCount'];				
				$titleString = "@$screen_name - $name: $imagineTweetCount Imagine tweets";
				echo "<div class='row'>";
				echo "<div class='large-1 columns'><a href='https://twitter.com/" . $screen_name . "'><img src='$profile_image_url' alt='$titleString' title='$titleString'/></a></div><div class='large-11 columns'><a href='https://twitter.com/" . $screen_name . "'>$titleString</a></div>\n";
				echo "</div>";
			}
		?>
		</div>		
	</div>

<div class="row">
	<div>&nbsp;</div>
</div>

<div class="row"> 
	<div class="large-12 columns">
		Users Posting The Most Photos:
		<div class="row">&nbsp;</div>
		<?php
			$query = "SELECT * FROM users ORDER BY imaginePhotos DESC LIMIT 5";
			$res = $mi->query($query);
		?>
		<?php
			while ($row = $res->fetch_assoc()) {
				$screen_name = $row['screen_name'];
				$name = $row['name'];
				$profile_image_url = $row['profile_image_url'];
				$url = $row['url'];
				$description = $row['description'];
				$imagineTweetCount = $row['imagineTweetCount'];				
				$imaginePhotos = $row['imaginePhotos'];
				$titleString = "@$screen_name - $name: $imaginePhotos Imagine photos";
				echo "<div class='row'>";
				echo "<div class='large-1 columns'><a href='https://twitter.com/" . $screen_name . "'><img src='$profile_image_url' alt='$titleString' title='$titleString'/></a></div><div class='large-11 columns'><a href='https://twitter.com/" . $screen_name . "'>$titleString</a></div>\n";
				echo "</div>";
			}
		?>			
	</div>
</div>

<div class="row">
	<div>&nbsp;</div>
</div>

	<div class="row">
		<div class="large-12 columns">
		Most Retweeted Tweets:
		<div class="row">&nbsp;</div>
		<?php
			$query = "SELECT * FROM tweets WHERE tweet_text NOT LIKE '%RT %' ORDER BY retweet_count DESC LIMIT 5";
			$res = $mi->query($query);
		?>
		<?php
			while ($row = $res->fetch_assoc()) {
				$tweet_id = $row['tweet_id'];
				$screen_name = $row['screen_name'];
				$name = $row['name'];
				$profile_image_url = $row['profile_image_url'];
				$tweet_text = stripcslashes($row['tweet_text']);
				$retweet_count = $row['retweet_count'];
				$titleString = "@$screen_name - $name: $tweet_text ($retweet_count retweets)";
				echo "<div class='row'>";
				echo "<div class='large-1 columns'><a href='https://twitter.com/" . $screen_name . "'><img src='$profile_image_url' alt='$titleString' title='$titleString'/></a></div><div class='large-11 columns'><a href='https://twitter.com/statuses/" . $tweet_id . "'>$titleString</a></div>\n";
				echo "</div>";
			}
		?>
		</div>		
	</div>

<div class="row">
	<div>&nbsp;</div>
</div>

	<div class="row">
		<div class="large-12 columns">
		Most Popular Twitter Platforms/Apps:
		<div class="row">&nbsp;</div>
		<?php
			$query = "SELECT DISTINCT source FROM tweets";
			$res = $mi->query($query);
		?>
		<?php
			$counts = array();
			while ($row = $res->fetch_assoc()) {
				$source = $row['source'];
				$query2 = "SELECT * FROM tweets WHERE source ='$source'";
				$res2 = $mi->query($query2);
				$numResults = $res2->num_rows; 
				$counts[$source] = $numResults;
			}	
			$num = 1;
			arsort(&$counts);
			foreach ($counts as $key => $value) {
				if($num < 6) {
					echo "<div class='row'>";
					echo "<div class='large-12 columns'>$key - $value tweets</div>\n";
					echo "</div>";					
				}
				$num++;
			}
		?>
		</div>		
	</div>


<div class="row">
	<div>&nbsp;</div>
</div>

	<div class="row">
		<div class="large-12 columns">
		Other Random Stats:
		<div class="row">&nbsp;</div>
		<?php
			$query = "SELECT tweet_text FROM tweets";
			$res = $mi->query($query);
			$tweetCount = $res->num_rows;
		?>
		<?php
					echo "<div class='row'>";
					echo "<div class='large-12 columns'>$tweetCount total Magento Imagine 2015 tweets.</div>\n";
					echo "</div>";					
		?>
		<div class='row'>
			<div class='large-12 columns'>
				<?php
					$query = "SELECT * FROM `tweets` WHERE lcase(tweet_text) LIKE '%magentoimagine%'";
					$res = $mi->query($query);
					$magentoImagineCount = $res->num_rows; 
					$query = "SELECT * FROM `tweets` WHERE lcase(tweet_text) LIKE '%imaginecommerce%'";
					$res = $mi->query($query);
					$imagineCommerceCount = $res->num_rows;
					echo "There have been $imagineCommerceCount #ImagineCommerce tweets vs $magentoImagineCount #MagentoImagine tweets. <br/>For 2015, the official hashtag has changed to #ImagineCommerce."; 
				?>				
			</div>
		</div>
		</div>		
	</div>


<div class="row">
	<div>&nbsp;</div>
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
