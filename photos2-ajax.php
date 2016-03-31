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
  <link rel="stylesheet" href="css/foundation.css" />
    <script src="js/vendor/modernizr.js"></script>
    <script src="//ajax.googleapis.com/ajax/libs/jquery/2.1.0/jquery.min.js"></script>
  <script src="js/freewall.js"></script>
<style type="text/css">
			body {
				background: black;
			}
			.free-wall {
				margin: 0px;
			}
			.brick img {
				margin: 0;
				display: block;
			}
		</style>
</head>
<body>
<?php
	require_once('menu.php');
?>
<div id="freewall" class="free-wall">
	
		<?php
			$query = "SELECT tweet_id, imageUrl FROM tweet_urls WHERE isImage = 1 GROUP BY url ORDER BY tweet_id DESC ";
			$res = $mi->query($query);
			$w = 1;
			$h = 1;
			$i = 0;
            $firstRow = true;
        ?>
		<?php
			echo "<!-- Found " . $res->num_rows . " images -->";
			while ($row = $res->fetch_assoc()) {
				$imageUrl = $row['imageUrl'];
                $tweet_id = $row['tweet_id'];
/*
				$query2 = "SELECT tweet_id FROM tweet_urls WHERE imageUrl = '" . $imageUrl . "'";
				$res2 = $mi->query($query2);
				$row2 = $res2->fetch_assoc();
				$tweet_id = $row2['tweet_id'];
 */
                if($firstRow == true) {
                    $firstRow = false;
                    $lastTweet = $tweet_id;
                }
				$query3 = "SELECT * FROM tweets WHERE tweet_id = $tweet_id";
				$res3 = $mi->query($query3);
				$row3 = $res3->fetch_assoc();
				$tweet_text = $row3['tweet_text'];
				$tweet_text = stripslashes($tweet_text); 
				// echo "<!--- $tweet_text --->";
				$screen_name = $row3['screen_name'];
                $tweet_text = str_replace('"', "'", $tweet_text);
				$titleString = "$screen_name: $tweet_text";
				$random = mt_rand() / 10000000000;
				$w = 1 + 3 * $random;
				$width = $w * 160;
                $width = round($width);
				// echo "Random is: $random\n";
				$i++;
                if(strpos($tweet_text, 'RT') === false ) {
                 // only include if it's not an RT
                    echo "<div class='brick' style='width: $width" . "px;' data-fixSize=true>";
                    echo "<a href='https://twitter.com/statuses/" . $tweet_id . "'><img width='100%' src='$imageUrl' ";
                    echo 'alt="' . $titleString . '"';
                    echo "' title=";
                    echo '"';
                    echo $titleString;
                    echo '"';
                    echo "/></a></div>";
                }
			}
		?>
</div>
<?php
	// die();
?>
  <script>
var wall = new freewall("#freewall");
			wall.reset({
				selector: '.brick',
				animate: true,
				gutterY: 0,
				gutterX: 0,
				cellW: 250,
				cellH: 'auto',
				onResize: function() {
					wall.fitWidth();
				}
			});

			var images = wall.container.find('.brick');
			images.find('img').load(function() {
				wall.fitWidth();
			});


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
    function processResults(resData) {
        var domLastTweet = parseInt(freewall.data("lastTweet"));
        // console.log("Last Tweet Per DOM: ");
        // console.log(domLastTweet);
        var phpLastTweet = parseInt(resData.lastTweet);
        // console.log("Last Tweet Per PHP: ");
        // console.log(phpLastTweet);
        // console.log("Response data:");
        // console.log(resData);
        var numResults = 0;
        if(phpLastTweet > domLastTweet) {
            // console.log("++ AJAX call gave us a newer tweet");
            freewall.data("lastTweet", phpLastTweet);
            $.each(resData.photos, function(i, item) {
                freewall.prepend(item);
                numResults++;
            })
            if(numResults > 0) {
                wall.fitWidth();
            }
        } else {
            // console.log("-- AJAX call did not give us a newer tweet.");
        }
    }
    var ResInterval = window.setInterval('myAjaxCall()', 60000); // 60 seconds = 60000
    var freewall = $("#freewall");
    freewall.data("lastTweet", "<?php echo $lastTweet; ?>");
    freewall.data("ajaxUrlRoot", "photoUpdates.php");
    var myAjaxCall = function() {
        var url = freewall.data("ajaxUrlRoot");
        var inputData = {
            "ajax": "1",
            "lastTweet": freewall.data("lastTweet")
        };
        var numResults = 0;
        var resData = null;
        $.getJSON(url, inputData, function( data ){
            processResults(data);
        });
    }
  </script>
</body>
</html>
