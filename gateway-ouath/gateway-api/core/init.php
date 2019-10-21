<?php

define ( 'APP_DIR' , BASE_DIR . '/app'. DS);
define ( 'CORE_DIR' , BASE_DIR . '/core'. DS);
define ( 'ROUTE_DIR' , BASE_DIR . '/route'. DS);
define ( 'CONFIG_DIR' , CORE_DIR . '/config'. DS);
include_once(CORE_DIR . 'helper.php');

//get env
$dotenv = Dotenv\Dotenv::create(BASE_DIR);
$dotenv->load();

define ( 'TOKEN_EXT' , getenv('TOKEN_EXT'));
define ( 'RESOURCE_EXT' , getenv('RESOURCE_EXT'));
define ( 'PROJECT_EXT' , getenv('PROJECT_ROOT'));

foreach (glob(CONFIG_DIR.'*.php') as $filename)
{
    include_once $filename;
}

foreach (glob(ROUTE_DIR.'*.php') as $filename)
{
    include_once $filename;
}






?>