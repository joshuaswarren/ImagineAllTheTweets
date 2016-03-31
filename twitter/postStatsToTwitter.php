<?php

require_once('codebird-php/src/codebird.php');
require_once('db_memcache.php);
Codebird::setConsumerKey("a3N6hm0ieEsvkqHXaFwCiAkfE", "grPlAMuI1CMaoq90roBseDnm27LBNnXWYgw4i8ogDMYVaeL2KL");
$cb = Codebird::getInstance();
$cb->setToken("2486807702-bkIjj9trxjBr0hN1FqEYkY1nvmXkM03p01m92IN", "cH6Qa8tim0TBXnRYhF1P1K4XYu7oUvTMHX8r4uyFWP2IS");
 
$postStats = false;
$myParams = array();
$query = "SELECT * FROM users ORDER BY imagineTweetCount DESC LIMIT 5";
$res = $mi->query($query);
$pos = 1;
while ($row = $res->fetch_assoc()) {
	$screen_name = $row['screen_name'];
        $pos++;
        echo "$screen_name is in position $pos\n";
}

if ($postStats == true)
{
        $reply = $cb->statuses_update($myParams);
}

