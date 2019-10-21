<?php

use Monolog\Logger;
use Monolog\Handler\StreamHandler;

function vd($var){
    echo "<pre>";
    var_dump($var);
}

function dd($var){
    echo "<pre>";
    var_dump($var);
    die;
}

$start_time =  microtime(true);
$response_time =  microtime(true);


function logTime($message){
    global $start_time;

    $end_time = microtime(true);
    echo "<br>";
    echo ": =>".($end_time -$start_time)."<="."<br>". $message;
    $start_time = $end_time;
}

function getDirName($path,$quantity){

    for ($i=0; $i<$quantity; $i++){
        $path =  dirname($path);
    }

    return $path;
}

function curl($url_invoke, $method, $payload, $headers){
    $ch = curl_init($url_invoke);

    $method !== 'GET' ? curl_setopt( $ch, CURLOPT_POSTFIELDS, $payload) : null;
    curl_setopt( $ch, CURLOPT_POSTFIELDS, $payload);
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT , 7);
    curl_setopt( $ch, CURLOPT_CUSTOMREQUEST, $method);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt( $ch, CURLINFO_HEADER_OUT, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

    $response = curl_exec($ch);

    if(curl_errno($ch)){
        echo 'Request Error:' . curl_error($ch);
    }
    curl_close($ch);

    return $response;
}

function logTimeStart(){
    global $response_time;
    $end_time = microtime(true);
    $response_time = $end_time;
}

function logResponseTime(){
    global $response_time;

    $end_time = microtime(true);
    $respTime = $end_time -$response_time;
    $response_time = $end_time;
    return $respTime;
}

function loggerService($serviceName,$userId, $url){

    $logger = new Logger('INFO LOGGER');
    $logger->pushHandler(new StreamHandler(BASE_DIR.'/storage/logs/'.$serviceName.'/log', Logger::INFO));
    $respTime = logResponseTime();

    $logger->info('Login user from dashboard', array('user_id'=>$userId, 'request_url'=>$url,'time'=>date('H:i:s d/m/Y'), 'resp_time'=>$respTime));
}
?>