<?php
namespace Service\Gateway;


class ReceiverService implements IReceiverService
{
    public function receiver($serviceName, $callback){
        $gatewayPort = getenv('GATEWAY_PORT');

        $url_invoke = 'http://'.$_SERVER['HTTP_HOST'];
        $url_invoke = str_replace($_SERVER['SERVER_PORT'], $gatewayPort, $url_invoke)."/".getenv('GATEWAY_DIR');

            $json = [
                'serviceName'=>$serviceName,
                'callback'=>$callback,
            ];

            $headers = array(
            'Content-Type: application/json',
            'Content-Length: ' . strlen(json_encode($json)));

          return curl($url_invoke, $json, 'POST',$headers);
    }
}