<?php


Predis\Autoloader::register();

$redis = new Predis\Client(array(
    'cluster' => false,

    'default' => [
        'host' => getenv('REDIS_HOST', '127.0.0.1'),
        'password' => getenv('REDIS_PASSWORD', null),
        'port' => getenv('REDIS_PORT', 6379),
        'database' => 3,
    ],
    'options' => [
        'parameters' => ['password' => getenv('REDIS_PASSWORD', null)],
    ],));


if (!$redis){
echo    'Not connected to redis';
exit();
}

?>
