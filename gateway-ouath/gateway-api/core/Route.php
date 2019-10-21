<?php

namespace Core;

use Core\Model\Response;

session_start();

class Route
{
    public static $group_name = '';
    public static $trigger_api = '';

    public static function parse_url()
    {
        $dirname = dirname($_SERVER['SCRIPT_NAME']);
        $basename = basename($_SERVER['SCRIPT_NAME']);
        $request_uri = str_replace([$dirname, $basename], null, $_SERVER['REQUEST_URI']);

        return $request_uri;
    }

    public static function group($groupname, callable ...$var)
    {
        self::$group_name = null;
        self::$trigger_api = null;
        !isset($groupname['prefix']) ? $groupname['prefix'] = '' : $groupname['prefix'];
        self::$group_name = $groupname['prefix'] . '/';
        self::$trigger_api = "/" . $groupname['trigger_api'];

        foreach ($var as $function) {
            if (is_callable($function)) {
                call_user_func($function, $groupname['prefix'], $groupname['trigger_api']);
            }
        }
    }

    public static function run($url, $callback, $method = 'get')
    {

        $callername = debug_backtrace()[0]['file'];
        $callername = self::lastParam('\\', $callername);

        $method = explode("|", strtoupper($method));
        if (in_array($_SERVER['REQUEST_METHOD'], $method)) {
            $patterns = [
                '{url}' => '([0-9a-zA-Z]+)',
                '{hashed_name}' => '([0-9a-zA-Z]+)',
                '{id}' => '(-?[0-9]\d*(.\d+)?$)'
            ];

            $url = str_replace(array_keys($patterns), array_values($patterns), $url);

            ($url == '') ? self::$group_name = str_replace('/', "", self::$group_name) : self::$group_name;
            $request_uri = self::parse_url();
            $url = self::$trigger_api . $callername . self::$group_name . $url;

            if (preg_match('@^' . $url . '$@', $request_uri, $parameters)) {

                $data = json_decode(file_get_contents("php://input"), true);

                if (isset(self::resourceServerControl()->success) && self::resourceServerControl()->success) {
                    $url_invoke = 'http://' . $_SERVER['HTTP_HOST'] . $request_uri;
                    $data['user_id'] = $_SESSION['user_id'];
                    $payload = json_encode($data);
                    $serviceName = str_replace('/','',self::$trigger_api);
                    $loader_JSON = BASE_DIR . '\gateway_config.json';
                    $registration = json_decode(file_get_contents($loader_JSON));
                    $url_invoke = str_replace($_SERVER['SERVER_PORT'], $registration->$serviceName->port . PROJECT_EXT, $url_invoke);

                    $headers = array(
                        'Content-Type: application/json',
                        'Content-Length: ' . strlen($payload));

                    global $redis;
                    if ($redis->get($url.$payload)) {
                        logTimeStart();
                        $response = json_decode($redis->get($url.$payload));
                        $response = new Response($response->success, $response->messages, $response->data, $response->statusCode);
                        $response->send();
                        loggerService($serviceName.'-cached',$_SESSION['user_id'],$url_invoke);

                    } elseif (!$redis->get($url.$payload)) {
                        logTimeStart();
                        $response = curl($url_invoke, $method[0], $payload, $headers);
                        loggerService($serviceName,$_SESSION['user_id'],$url_invoke);
                        $response = is_null(json_decode($response)) ? $response : json_decode($response);

                        //set cache
                        $response->success== true ? $redis->set($url.$payload, json_encode($response)) : null;
                        //clear cache if return status code is belong to CREATE, DELETE method or edited entry
                        !($response->statusCode == 201  || $response->statusCode == 202) ?: $redis->flushAll() ;

                        $response = new Response($response->success, $response->messages, $response->data, $response->statusCode);
                        $response->send();
                    } else {
                        $response = new Response(false, self::resourceServerControl()->error_description, [], 403);
                        $response->send();
                        exit;
                    }
                }
            }
        }
    }

    public static function resourceServerControl()
    {
        $url_invoke = getDirName(__FILE__, 3);
        $url_invoke = str_replace('\\', '/', $url_invoke);
        $url_invoke = str_replace($_SERVER['DOCUMENT_ROOT'], 'http://' . $_SERVER['HTTP_HOST'], $url_invoke);
        $url_invoke = $url_invoke . RESOURCE_EXT;
        $payload = 'access_token=' . $_SESSION['HTTP_AUTHORIZATION'];
        $ch = curl_init($url_invoke);

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLINFO_HEADER_OUT, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);

        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                'Content-Type: application/x-www-form-urlencoded',
                'Content-Length: ' . strlen($payload)
            )
        );

        $result = curl_exec($ch);
        $result = json_decode($result);
        curl_close($ch);

        return $result;
    }

    public static function request($url, $callback, $method = 'get')
    {
        $callername = debug_backtrace()[0]['file'];
        $callername = self::lastParam('\\', $callername);

        $method = explode("|", strtoupper($method));

        if (in_array($_SERVER['REQUEST_METHOD'], $method)) {
            $patterns = [
                '{url}' => '([0-9a-zA-Z]+)',
                '{hashed_name}' => '([0-9a-zA-Z]+)',
                '{id}' => '(-?[0-9]\d*(.\d+)?$)'
            ];

            $url = str_replace(array_keys($patterns), array_values($patterns), $url);

            ($url == '') ? self::$group_name = str_replace('/', "", self::$group_name) : self::$group_name;

            $request_uri = self::parse_url();
            $url = self::$trigger_api . $callername . self::$group_name . $url;


            if (preg_match('@^' . $url . '$@', $request_uri, $parameters)) {
                $parameters = explode('/', $parameters[0]);
                $len = count($parameters);

                if (is_callable($callback)) {
                    call_user_func_array($callback, $parameters);
                }

                $controller = explode('@', $callback);

                $className = explode('/', $controller[0]);
                $className = ucfirst(end($className)) . 'Controller';
                $dir = str_replace("\\", "/", APP_DIR);
                $controllerFile = $dir . 'controller/' . $className . '.php';

                if (file_exists($controllerFile)) {

                    $className = 'App\\Controller\\' . $className;
                    require_once $controllerFile;

                    call_user_func_array([new $className, $controller[1]], $parameters);
                }
            }
        }
    }

    public static function lastParam($delimiter, $str)
    {
        $str = explode($delimiter, $str);
        $len = count($str);
        for ($i = 0; $i < $len - 1; $i++) {
            unset($str[$i]);
        }
        $str = str_replace('.php', '/', $str);
        return '/' . $str[$len - 1];
    }

}