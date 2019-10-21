<?php
$db_vars = [
    "driver" => "mysql",
    "host" =>getenv('DB_HOST'),
    "database" => getenv('DB_NAME'),
    "username" => getenv('DB_USER'),
    "password" => getenv('DB_PASS'),
    "port" => getenv('DB_PORT'),
];
?>