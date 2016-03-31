<?php

require_once('db_credentials.php);

$mi = new mysqli($dbhost, $dbuser, $dbpass, $dbname);

mysqli_query($mi, 'SET NAMES "utf8"');
mysqli_query($mi, 'SET CHARACTER SET "utf8"');
mysqli_query($mi, 'SET character_set_results = "utf8",' .
'character_set_client = "utf8", character_set_connection = "utf8",' .
'character_set_database = "utf8", character_set_server = "utf8"');
$mi->query('SET NAMES utf8');

function convertTime($twitterTime) {
	// converts from Twitter Time to Las Vegas time in MySQL format
	$time = strtotime($twitterTime);
	$timeShifted = $time - (3600 * 7);
	$correctTime = gmdate('Y-m-d H:i:s', $timeShifted);
	return $correctTime;
}

function cleanString($text, $mi) {
    $clean = mysqli_real_escape_string($mi, $text);
    return $clean;
}

function convertEmptyToNull($text) {
	if(strlen($text) == 0)
		return 'null'; 
	else
		return $text;
}

function prepareStatementUser($mi) {
	$query = "REPLACE INTO users ( 
									user_id,
									screen_name,
									name,
									profile_image_url,
									location,
									url,
									description,
									created_at,
									followers_count,
									friends_count,
									statuses_count,
									time_zone
								 )
								 VALUES
								 (
									?,
									?,
									?,
									?,
									?,
									?,
									?,
									?,
									?,
									?,
									?,
									?
								 )";	
	$stmt = $mi->prepare($query);									
	if($stmt === false) {
	  die ('Error preparing statement');
	}
	return $stmt;
	
}

function prepareStatementImage($mi) {
	$query = "SELECT tweet_id, tweet_text FROM tweets WHERE tweet_text LIKE '%http%' AND checkedForImages = 0";
	$stmt = $mi->prepare($query);									
	if($stmt === false) {
	  die ('Error preparing statement');
	}
	return $stmt;	
}


function prepareStatement($mi) {
		$query = "REPLACE INTO tweets (	
										tweet_id, 
										tweet_text, 
										entities, 
										created_at, 
										geo_lat, 
										geo_long, 
										user_id, 
										screen_name,
										name, 
										profile_image_url, 
										source, 
										in_reply_to_status_id,
										in_reply_to_status_id_str, 
										in_reply_to_user_id, 
										in_reply_to_user_id_str,
										in_reply_to_screen_name,
										retweet_count,
										favorite_count 
									) 
										VALUES
									(	
										?,
										?,
										?,
										?,
										?,
										?,
										?,
										?,
										?,
										?,
										?,
										?,
										?,
										?,
										?,
										?,
										?,
										?										
									)";	
      $stmt = $mi->prepare($query);									
      if($stmt === false) {
	      die ('Error preparing statement');
      }
      return $stmt;
}


function getUrlContents($url) {
/*
	global $m;
	$contents = $m->get($url);
	if($contents !== false) {
		echo "Fetched $url from cache\n";
		return $contents;
	}	
	*/
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_HEADER, 1);
	curl_setopt($ch, CURLOPT_AUTOREFERER, 1);
	curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	$res = curl_exec($ch);
	curl_close($ch);
	$contents = $res;
//	$m->set($url, $contents);
	return $res;
}

function checkContentsForTwitterPhotos($contents) {
	$pos = strpos($contents, 'media-slideshow-image');
	if($pos !== false) {
		$httpsPos = strpos($contents, 'https', $pos);
		$nextQuote = strpos($contents, '"', $httpsPos);
		$length = $nextQuote - $httpsPos;
		$imageLink = substr($contents, $httpsPos, $length);
		return $imageLink;
	}
	return null;
}

function findNextImage($contents, $offset = 0) {
	$pos = strpos($contents, '<img src="http://', $offset);
	if($pos !== false) {
		$httpPos = strpos($contents, "http://", $pos);
		if($httpPos !== false) {
			$quotePos = strpos($contents, '"', $httpPos);
			$length = $quotePos - $httpPos;
			$imageLink = substr($contents, $httpPos, $length); 
		} else {
			return null;
		}
	} else {
		return null;
	}	
	$offset = $quotePos;
	$result = array('image' => $imageLink, 'offset' => $offset); 
	return $result;	
}

function checkContentsForTwitPic($contents) {
	$result = findNextImage($contents);
	$foundProfileImage = false;
	while(!is_null($result)) {
		$image = $result['image'];
		$offset = $result['offset'];
		$offset = $result['offset'];
		$pos = strpos($image, 'profile_images');
		if($pos !== false) {
			$foundProfileImage == true;
		}
		if($pos === false && $foundProfileImage) {
			return $image;
		} else {
			$result = findNextImage($contents, $offset);
		}	
	}
	return null;
}

function checkContentsForImages($contents) {
	$twitterPhoto = checkContentsForTwitterPhotos($contents);
	if(!is_null($twitterPhoto)) {
		return $twitterPhoto;
	}
	$twitPicPhoto = checkContentsForTwitPic($contents);
	if(!is_null($twitPicPhoto)) {
		return $twitPicPhoto;
	}	
	return null;	
}

function checkUrlsForImages($mi) {
	$query = "SELECT DISTINCT url, tweet_id FROM tweet_urls WHERE checked = 0";
	$res = $mi->query($query);
	while($row = $res->fetch_assoc()) {
		$tweet_id = $row['tweet_id'];
		$url = $row['url'];
		$contents = getUrlContents($url);
		$image = checkContentsForImages($contents);
		if(!is_null($image)) {
			echo "Found an image: $image - $tweet_id\n";
			$query2 = "UPDATE tweet_urls SET imageUrl = '" . $image . "', isImage = 1, checked = 1 WHERE tweet_id = $tweet_id";
			$res2 = $mi->query($query2);
		} else {
			echo "No image found - $tweet_id - $url\n";
		}
	}
}

function updateImagineTweetCount($mi) {
	$query = "SELECT user_id FROM users";
	$res = $mi->query($query);
	while($row = $res->fetch_assoc()) {
		$imaginePhotos = 0;
		$id = $row['user_id'];
		$query2 = "SELECT tweet_id FROM tweets WHERE user_id = $id";
		$res2 = $mi->query($query2);
		$tweets = $res2->num_rows;
		while($row2 = $res2->fetch_assoc()) {
			$tweet_id = $row2['tweet_id'];
			$queryZ = "SELECT * FROM tweet_urls WHERE tweet_id = $tweet_id AND isImage = 1";
			$resZ = $mi->query($queryZ);
			if($resZ->num_rows > 0) {
				$imaginePhotos = $imaginePhotos + $resZ->num_rows; 
			}
		}
		$query3 = "UPDATE users SET imagineTweetCount = $tweets, imaginePhotos = $imaginePhotos WHERE user_id = $id";
		$res3 = $mi->query($query3);
	}
}

function insertImage($id, $url, $mi) {
	$url = trim($url, ')');
	$query = 'UPDATE tweets SET checkedForImages = 1 WHERE tweet_id = ' . $id;
	$res = $mi->query($query);
	if($res === false) {
		echo "Error in $query " . $mi->error . "\n";
	}
	$query = 'REPLACE INTO tweet_urls (tweet_id, url) VALUES (' . $id . ', "' . $url . '")';
	$res = $mi->query($query);
		if($res === false) {
		echo "Error in $query " . $mi->error . "\n";
	}
}

function extractUrl($text) {
	$pieces = explode(' ', $text);
	$urls = array();
	$foundUrls = 0;
	foreach ($pieces as $piece) {
		if(strpos($piece, 'http://') !== false) {
			$urls[] = $piece;
			$foundUrls++;
		}
	}
	if($foundUrls == 0) {
		return null;
	} else {
		return $urls;
	}
}

function checkForImages($mi) {
	$query = "SELECT tweet_id, tweet_text FROM tweets WHERE tweet_text LIKE '%http%' AND checkedForImages = 0";
	$res = $mi->query($query);
	while($row = $res->fetch_assoc()) {
		$text = $row['tweet_text'];
		$id = $row['tweet_id'];
		$urls = extractUrl($text);
		if(!is_null($urls)) {
			foreach($urls as $url) {
				insertImage($id, $url, $mi);				
			}
		}	
	}
}

/*
$m = new Memcached();
$m->addServer('localhost', 11211);
*/

$stmt = prepareStatement($mi);
$userStmt = prepareStatementUser($mi);
$imageCheckStmt = prepareStatementImage($mi);




