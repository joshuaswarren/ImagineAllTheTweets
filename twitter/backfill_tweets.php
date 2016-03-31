<?php
error_reporting(E_ALL);
ini_set('error_reporting', E_ALL);
ini_set('display_errors',1);
require_once('db_memcache.php');
require_once ('codebird-php/src/codebird.php');
define("BACKFILL_AMOUNT", 100);


$geoLog = 'geo.log';

$key = 'yfW41Xi9hyD3MeVptXjNw';
$secret = '5POleZHcXdV5mOZMyDM1wAMjH3S8R0FRszsBcNs';

$userToken = '293383320-GQ7hYNFFdvsR1D1wiT1DP98cXqse5DTQE31ef691';
$userSecret = 'NphoBzOnIVWgqpZqCKrfO3gB7YX7K9wmb24mSHLBYc';

Codebird::setConsumerKey($key, $secret); // static, see 'Using multiple Codebird instances'

function processResults($result, $mi, $stmt, $userStmt) {
	foreach($result->statuses as $status) {
		$tweet_id = $status->id;
		$tweet_text = cleanString($status->text, $mi);
		$entities = convertEmptyToNull('');
		$created_at = convertTime($status->created_at);	
		if(isset($status->geo->coordinates))
		{
	      $geo_lat = $status->geo->coordinates[0];
	      $geo_long = $status->geo->coordinates[1];
		} else {
			$geo_lat = 'null';
			$geo_long = 'null';
		}
		$user_id = $status->user->id;
		$screen_name = $status->user->screen_name;
		$name = $status->user->name;
		$profile_image_url = $status->user->profile_image_url;
		$source = strip_tags($status->source);
		$in_reply_to_status_id = convertEmptyToNull($status->in_reply_to_status_id);
		$in_reply_to_status_id_str = convertEmptyToNull($status->in_reply_to_status_id_str);
		$in_reply_to_user_id = convertEmptyToNull($status->in_reply_to_user_id);
		$in_reply_to_user_id_str = convertEmptyToNull($status->in_reply_to_user_id_str);
		$in_reply_to_screen_name = convertEmptyToNull($status->in_reply_to_screen_name);
		$retweet_count = $status->retweet_count;
		$favorite_count = $status->favorite_count;

		$stmt->bind_param('isssddissssisissii', $tweet_id, $tweet_text, $entities, $created_at, $geo_lat, $geo_long,
								$user_id, $screen_name, $name, $profile_image_url, $source, $in_reply_to_status_id,
								$in_reply_to_status_id_str, $in_reply_to_user_id, $in_reply_to_user_id_str,
								$in_reply_to_screen_name, $retweet_count, $favorite_count);
		$res = $stmt->execute();
		if($res === false) {
			echo "\nError $stmt->errno $stmt->error for tweet $tweet_id with content $tweet_text - https://twitter.com/$screen_name/status/$tweet_id\n";	
		}
		
		$location = cleanString($status->user->location, $mi);
		$url = $status->user->url;
		$description = cleanString($status->user->description, $mi);
		$user_created_at = convertTime($status->user->created_at);
		$followers_count = $status->user->followers_count;
		$friends_count = $status->user->friends_count;
		$statuses_count = $status->user->statuses_count;
		$time_zone = $status->user->time_zone;
		
		$userStmt->bind_param('isssssssiiis', $user_id, $screen_name, $name, $profile_image_url, 
									$location, $url, $description, $user_created_at, $followers_count,
									$friends_count, $statuses_count, $time_zone);
		$res = $userStmt->execute();
		if($res === false) {
			echo "\nError $userStmt->errno $userStmt->error\n";	
		}
				
	}	
}


$cb = Codebird::getInstance();
$cb->setToken($userToken, $userSecret);

$params = array(
		'q' => 'magentoimagine', 
		'count' => BACKFILL_AMOUNT,
		'include_entities' => 'true',
);
$result = $cb->search_tweets($params);
processResults($result, $mi, $stmt, $userStmt);

$params = array(
		'q' => 'imagine2015', 
		'count' => BACKFILL_AMOUNT,
		'include_entities' => 'true',
);
$result = $cb->search_tweets($params);
processResults($result, $mi, $stmt, $userStmt);

$params = array(
		'q' => 'preimagine', 
		'count' => BACKFILL_AMOUNT,
		'include_entities' => 'true',
);
$result = $cb->search_tweets($params);
processResults($result, $mi, $stmt, $userStmt);

$params = array(
		'q' => 'magentohackathon', 
		'count' => BACKFILL_AMOUNT,
		'include_entities' => 'true',
);
$result = $cb->search_tweets($params);
processResults($result, $mi, $stmt, $userStmt);

$params = array(
		'q' => 'imaginecommerce', 
		'count' => BACKFILL_AMOUNT,
		'include_entities' => 'true',
);
$result = $cb->search_tweets($params);
processResults($result, $mi, $stmt, $userStmt);

$params = array(
		'q' => 'roadtoimagine', 
		'count' => BACKFILL_AMOUNT,
		'include_entities' => 'true',
);
$result = $cb->search_tweets($params);
processResults($result, $mi, $stmt, $userStmt);

$params = array(
		'q' => 'roadfromimagine', 
		'count' => BACKFILL_AMOUNT,
		'include_entities' => 'true',
);
$result = $cb->search_tweets($params);
processResults($result, $mi, $stmt, $userStmt);


$params = array(
		'q' => 'magento', 
		'count' => BACKFILL_AMOUNT,
		'include_entities' => 'true',
		'geocode' => '36.1749687195, -115.1372222900, 10mi',
);
echo "Getting Results\n"; 
$result = $cb->search_tweets($params);
echo "Processing Results\n";
processResults($result, $mi, $stmt, $userStmt);
echo "Checking for Images\n";
checkForImages($mi, $imageCheckStmt);
echo "Updating Tweet Count\n";
updateImagineTweetCount($mi);
echo "Checking URLs for Images\n";
checkUrlsForImages($mi);
echo "Done!"; 
