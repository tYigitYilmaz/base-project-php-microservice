<?php
use Core\IOC\Kernel;

define ( 'APP_DIR' , BASE_DIR . '/app'. DS);
define ( 'CORE_DIR' , BASE_DIR . '/core'. DS);
define ( 'ROUTE_DIR' , BASE_DIR . '/route'. DS);
define ( 'CONFIG_DIR' , CORE_DIR . '/config'. DS);
include_once (CORE_DIR.'helper.php');

//get env
$dotenv = Dotenv\Dotenv::create(BASE_DIR);
$dotenv->load();


foreach (glob(CONFIG_DIR.'*.php') as $filename)
{
    include_once $filename;
}


$DB = new \Core\DB();
////logTime('IoC');
////IoC container'in cachelenerek kullanimi
//$closure_invoke = $memoize((new Kernel())->boot());
//$reflectionTypeIoC = new ReflectionFunction($closure_invoke);
//$kernel = ($closure_invoke == null ? (new Kernel())->boot() : $reflectionTypeIoC->getStaticVariables()['func']);
$kernel = ((new Kernel())->boot());

foreach (glob(ROUTE_DIR.'*.php') as $filename)
{
    include_once $filename;
}







?>