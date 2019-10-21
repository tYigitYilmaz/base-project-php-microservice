<?php

namespace Core;

use PDO;

class DBGenerator
{
    private static $DBConnection;

    public static function connectDB() {
        global $db_vars;
        if(self::$DBConnection === null) {
            self::$DBConnection = new PDO('mysql:host='.$db_vars['host'].';dbname='.$db_vars['database'].';charset=utf8', $db_vars['username'], $db_vars['password']);
            self::$DBConnection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            self::$DBConnection->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
        }
        return self::$DBConnection;
    }

}