<?php

session_cache_limiter('private_no_cache');
header('Cache-Control:private');

if (strstr($_SERVER['PHP_SELF'], 'run.inc.php')) {
    exit;
}
if (version_compare(PHP_VERSION, '8.0.0', '<')) {
    exit('require PHP > 8.0.0!');
}

const ROOT_DIR = __DIR__;
require ROOT_DIR . '/vendor/autoload.php';
$http = (new think\App())->http;
$response = $http->name(APP_NAME)->run();
$response->send();
$http->end($response);
