<?php

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


function logTime($message){
    global $start_time;
    $end_time = microtime(true);
    echo "<br>";
    echo ": =>".($end_time -$start_time)."<="."<br>". $message;
    $start_time = $end_time;
}

function curl($url_invoke, $json, $method, $headers){
    $ch = curl_init($url_invoke);

    curl_setopt( $ch, CURLOPT_CUSTOMREQUEST, $method);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt( $ch, CURLOPT_POSTFIELDS, json_encode($json));
    curl_setopt( $ch, CURLINFO_HEADER_OUT, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

    $response = curl_exec($ch);

    if(curl_errno($ch)){
        echo 'Request Error:' . curl_error($ch);
    }
    curl_close($ch);

    return $response;
}

$memoize = function($func)
{
    return function() use ($func)
    {
        static $cache = [];

        $args = func_get_args();
        $key = serialize($args);

        if ( !isset($cache[$key])) {
            $cache[$key] = call_user_func_array($func, $args);
        }

        return $cache[$key];
    };
};
?>