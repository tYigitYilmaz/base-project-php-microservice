<?php
namespace Core;
use Illuminate\Database\Capsule\Manager as Capsule;

class DB
{
    public function __construct()
    {
        global $db_vars;
        $capsule = new Capsule;
        $capsule->addConnection($db_vars);
        $capsule->bootEloquent();
    }

}
