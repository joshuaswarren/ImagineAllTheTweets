<?php

require_once('twitter/db_memcache.php');

if(!isset($_REQUEST['ajax'])) {
    die();
}

if(isset($_REQUEST['lastTweet'])) {
    $lastTweet = $_REQUEST['lastTweet'];
    $lastTweet = filter_var($lastTweet, FILTER_SANITIZE_NUMBER_INT);
    $originalLastTweet = $lastTweet;
    $query = "SELECT tweet_id, imageUrl FROM tweet_urls WHERE isImage = 1 AND tweet_id > " . $lastTweet . " GROUP BY url ORDER BY tweet_id DESC";
    $message = $_SERVER['REMOTE_ADDR'] . ": " . $query;
    // file_put_contents('/var/log/imagine14_photo.log', $message. "\n", FILE_APPEND);
} else {
    $query = "SELECT tweet_id, imageUrl FROM tweet_urls WHERE isImage = 1 GROUP BY url ORDER BY tweet_id DESC LIMIT 5";
}

$photos = array();
$firstRow = true;
$res = $mi->query($query);
while ($row = $res->fetch_assoc()) {
    $imageUrl = $row['imageUrl'];
    $tweet_id = $row['tweet_id'];
    if($firstRow === true) {
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
    $width = $w * 150;
    $width = round($width);
    // echo "Random is: $random\n";
    $i++;
    $outputString = '';
    $outputString .= "<div class='brick' style='width: $width" . "px;' data-fixSize=true>";
    $outputString .= "<a href='https://twitter.com/statuses/" . $tweet_id . "'><img width='100%' src='$imageUrl' ";
    $outputString .= 'alt="' . $titleString . '"';
    $outputString .= "' title=";
    $outputString .= '"';
    $outputString .= $titleString;
    $outputString .= '"';
    $outputString .= "/></a></div>";
    if(strpos($tweet_text, 'RT') === false ) {
        $photos[] = $outputString; // only include if it's not an RT
    }
}
$mi->close();

$data = array();
$data['lastTweet'] = $lastTweet;
$data['photos'] = $photos;
// $photos['lastTweet'] = $lastTweet;
// echo json_encode($photos);
$message = print_r($data, true);
$message = $message . "\n";
file_put_contents('/var/log/imagine14_photo.log', $message, FILE_APPEND);
echo json_encode($data);
