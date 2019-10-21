<?php
namespace App\Controller;



class GatewayController
{

    public function trigger(){

        $loader_JSON = BASE_DIR . '\gateway_config.json';
        $registration = json_decode(file_get_contents($loader_JSON));
        $data = json_decode(file_get_contents("php://input"));

        $serviceName = $data->serviceName;
        $callback = $data->callback;

        $conf = explode(",", $registration->$serviceName->$callback);
        $url = $conf[0];
        $method = $conf[1];

        $url_invoke = 'http://'.$_SERVER['HTTP_HOST'];
        $port = $registration->$serviceName->port;
        $url_invoke = str_replace($_SERVER['SERVER_PORT'], $port, $url_invoke).$url;

        $payload = json_encode($data);
        $headers = array(
            'Content-Type: application/json',
            'Content-Length: ' . strlen($payload));
        logTimeStart();
        $response = curl($url_invoke, $method, $payload, $headers);
        loggerService($serviceName,$_SESSION['user_id']);
        return $response;
    }


    public function register(){

        $loader_JSON = BASE_DIR . '\gateway_config.json';
        $registration = json_decode(file_get_contents($loader_JSON));
        $data = json_decode(file_get_contents("php://input"));

        $serviceName = 'user-service';
        $callback = 'register';

        $conf = explode(",", $registration->$serviceName->$callback);
        $url = $conf[0];
        $method = $conf[1];

        $url_invoke = 'http://'.$_SERVER['HTTP_HOST'];
        $port = $registration->$serviceName->port;
        $url_invoke = str_replace($_SERVER['SERVER_PORT'], $port, $url_invoke).$url;

        $payload = json_encode($data);
        $headers = array(
            'Content-Type: application/json',
            'Content-Length: ' . strlen($payload));

        return curl($url_invoke, $method, $payload, $headers);
    }

    public function login(){

        $loader_JSON = BASE_DIR . '\gateway_config.json';
        $registration = json_decode(file_get_contents($loader_JSON));
        $data = json_decode(file_get_contents("php://input"));

        $serviceName = 'user-service';
        $callback = 'login';


        $conf = explode(",", $registration->$serviceName->$callback);
        $url = $conf[0];
        $method = $conf[1];

        $url_invoke = 'http://'.$_SERVER['HTTP_HOST'];
        $port = $registration->$serviceName->port;
        $url_invoke = str_replace($_SERVER['SERVER_PORT'], $port, $url_invoke).$url;

        $payload = json_encode($data);
        $headers = array(
            'Content-Type: application/json',
            'Content-Length: ' . strlen($payload));

        $data = json_decode(curl($url_invoke, $method, $payload, $headers));
        $_SESSION['user_id'] = $data->data->user_id;
        $data = $data->data;
        $data = [
            'client_id' => $data->client_id,
            'client_secret' => $data->client_secret,
            'grant_type' => $data->grant_types,
        ];

        $serviceName = 'base-project';
        $callback = 'login';

        $conf = explode(",", $registration->$serviceName->$callback);
        $url = $conf[0];
        $method = $conf[1];

        $url_invoke = 'http://'.$_SERVER['HTTP_HOST'];
        $port = $registration->$serviceName->port;
        $url_invoke = str_replace($_SERVER['SERVER_PORT'], $port, $url_invoke).$url;

        $payload = json_encode($data);
        $headers = array(
            'Content-Type: application/json',
            'Content-Length: ' . strlen($payload),
            );

        logTimeStart();
        $ch = curl_init($url_invoke);

        curl_setopt( $ch, CURLOPT_CUSTOMREQUEST, $method);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt( $ch, CURLOPT_POSTFIELDS, $payload);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        $result = curl_exec($ch);
        $result = json_decode($result);

        curl_close($ch);
        $_SESSION['HTTP_AUTHORIZATION'] = $result->access_token;
        loggerService($serviceName,$_SESSION['user_id'],$url_invoke);

        return $result;
    }
}