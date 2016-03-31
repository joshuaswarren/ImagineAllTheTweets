<?php

error_reporting(0);
// error_reporting(E_ALL);

if (!$_GET) {
    parse_str($argv[1], $output);
    $_GET = $output;
}

if (!isset($_GET['q']) || !isset($_GET['callback'])) {
    header('HTTP/1.0 204 No Content', true, 204);
    exit();
}

$callback = $_GET['callback'];
$debug = $_GET['debug'];
unset($_GET['callback']);
unset($_GET['debug']);
unset($_GET['_']);

function cacheURL () {
    $url = array();
    foreach ($_GET as $key => $value) {
        array_push($url, $key . '=' . urlencode($value));
    }
    return implode($url, '&');
}

function cleanGET () {
    $_GET['q'] = urlencode($_GET['q']);
}

cleanGET();
$cacheURL = md5(cacheURL());

$full = $_GET['full'];
unset($_GET['full']);

include '../config.php';

if (CACHE_DURATION > 0) {
    include 'php_fast_cache.php';
    phpFastCache::$storage = 'auto';
    $data = phpFastCache::get($cacheURL);
}

if ($data === null || isset($debug)) {
    include 'connection/RestApi.php';

    /*
     * Config
     */
    $consumerKey = TWITTER_APP_CONSUMERKEY;
    $consumerSecret = TWITTER_APP_CONSUMERSECRET;
    $accessToken = TWITTER_APP_ACCESSTOKEN;
    $accessTokenSecret = TWITTER_APP_ACCESSTOKENSECRET;

    /*
     * Create new RestApi instance
     * Consumer key and Consumer secret are required
     * Access Token and Access Token secret are required to use api as a user
     */
    $twitter = new \TwitterPhp\RestApi($consumerKey, $consumerSecret, $accessToken, $accessTokenSecret);

    /*
     * Connect as application
     * https://dev.twitter.com/docs/auth/application-only-auth
     */
    $connection = $twitter->connectAsApplication();

    /*
     * Collection of the most recent Tweets posted by the user indicated by the screen_name, without replies
     * https://dev.twitter.com/docs/api/1.1/get/statuses/user_timeline
     */
    $results = $connection->get('/search/tweets', $_GET);
    $data = $results;

    if (!isset($debug)) {
        $data = array();

        if (isset($full)) {
            $data = $results['statuses'];
        } else {
            foreach ($results['statuses'] as $key => $value) {
                if ($value['coordinates']) {
                    $status = array(
                        'id' => $value['id'],
                        'created_at' => $value['created_at'],
                        'text' => $value['text'],
                        'geo' => $value['geo'],
                        'coordinates' => $value['coordinates'],
                        'place' => $value['place'],
                        'user' => $value['user'],
                        'entities' => $value['entities']
                    );
                    array_push($data, $status);
                }
            }
        }
    }

    if (CACHE_DURATION > 0) {
        // set data in to cache in 600 seconds = 10 minutes
        phpFastCache::set($cacheURL, $data, CACHE_DURATION);
    }
}

header('Content-Type: application/json');
echo $callback . '(' . json_encode($data) . ')';