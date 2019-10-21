<?php

namespace Core;

use Core\Model\Response;

class Route
{
    public static $group_name ='';

    public static function parse_url()
    {
        $dirname = dirname($_SERVER['SCRIPT_NAME']);
        $basename = basename($_SERVER['SCRIPT_NAME']);
        $request_uri = str_replace([$dirname, $basename], null, $_SERVER['REQUEST_URI']);
        return $request_uri;
    }

    public static function group($groupname,callable ...$var){
        self::$group_name = $groupname['prefix'].'/';

        foreach ($var as $function){
            if (is_callable($function)) {
                call_user_func($function,$groupname['prefix']);
            }
        }
    }

    public static function run($url, $callback, $method = 'get')
    {
        $callername=debug_backtrace()[0]['file'];
        $callername = self::lastParam('\\',$callername);

        $method = explode("|", strtoupper($method));

        if (in_array($_SERVER['REQUEST_METHOD'], $method)) {
            $patterns = [
                '{url}' => '([0-9a-zA-Z]+)',
                '{hashed_name}' => '([0-9a-zA-Z]+)',
                '{id}' => '(-?[0-9]\d*(.\d+)?$)'
            ];

            $url = str_replace(array_keys($patterns), array_values($patterns), $url);

            ($url == '') ? self::$group_name = str_replace('/',"",self::$group_name) : self::$group_name;

            $request_uri = self::parse_url();

            $url  = $callername.self::$group_name.$url;

           if (preg_match('@^' . $url . '$@', $request_uri, $parameters)) {
                $parameters = explode('/', $parameters[0]);
                $len = count($parameters);

                if (is_callable($callback)) {
                    call_user_func_array($callback, $parameters);
                }
                $controller = explode('@', $callback);

                $className = explode('/', $controller[0]);
                $className = ucfirst(end($className)) . 'Controller';
                $controllerFile = APP_DIR . 'controller/' . $className . '.php';
                if (file_exists($controllerFile)) {
                    $className = 'App\\Controller\\'.$className;
                    require_once $controllerFile;
//                    call_user_func_array([new $className, $controller[1]], $parameters);
                    global $kernel;
                    $kernel->getContainer()->getService($className)->{$controller[1]}(end($parameters));
//                    call_user_func_array([$kernel->getContainer()->getService($className), $controller[1]], $parameters);
                }
            }
        }
    }

    public static function lastParam($delimiter,$str){
        $str = explode($delimiter, $str);
        $len = count($str);
        for ($i=0; $i<$len-1; $i++){
            unset($str[$i]);
        }
        $str = str_replace('.php', '/', $str);
        return '/'.$str[$len-1];
    }

}