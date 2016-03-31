<?php
require_once ('codebird-php/src/codebird.php');
$key = 'yfW41Xi9hyD3MeVptXjNw';
$secret = '5POleZHcXdV5mOZMyDM1wAMjH3S8R0FRszsBcNs';

Codebird::setConsumerKey($key, $secret); // static, see 'Using multiple Codebird instances'

$cb = Codebird::getInstance();