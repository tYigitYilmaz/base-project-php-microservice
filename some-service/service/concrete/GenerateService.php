<?php
namespace Service\Concrete;


use Core\DBGenerator;
use Core\Model\Response;
use PDO;
use Service\Abst\IGenerateService;

class GenerateService implements IGenerateService
{
    private $DB;

    public function __construct()
    {
        try {
            $this->DB = DBGenerator::connectDB();
            return $this->DB;

        } catch (PDOException $ex) {
            // log connection error for troubleshooting and return a json error response
            error_log("Connection Error: " . $ex, 0);
            $response = new Response(false, ['Database connection error'], $ex, 500);
            $response->send();
            exit;
        }
    }

    public function generate(){

        $table = getenv('DB_NAME');

        $info = $this->keyProvider($table);
        $tables = $this->showColumns($table);

                $this->createEntity($tables);
                $this->createRepo($tables);
        vd($tables);
        dd($info);
    }

    public function keyProvider($table){
        $general_info_PK = [];
        $general_info_FK = [];
        $info = $this->DB->query("
                        SELECT
                        TABLE_NAME,
                        COLUMN_NAME,
                        CONSTRAINT_SCHEMA,
                        CONSTRAINT_NAME,
                        REFERENCED_TABLE_SCHEMA,
                        REFERENCED_TABLE_NAME,
                        REFERENCED_COLUMN_NAME
                        FROM INFORMATION_SCHEMA.KEY_COLUMN_USAGE
                        where constraint_schema = '$table';"
        );
        $info->setFetchMode(PDO::FETCH_ASSOC);
        $result = $info->fetchAll();

        foreach ($result as $key=>$tableInfo) {
            if ($tableInfo['CONSTRAINT_NAME'] == 'PRIMARY'){
                $general_info_PK[$tableInfo['TABLE_NAME'].'-PK-'.$key] = [
                    'primary_key' => $tableInfo['COLUMN_NAME'],

                ];
            } else {
                $general_info_FK[$tableInfo['TABLE_NAME'].'-FK-'.$key] = [
                    'foriegn_key_table' => $tableInfo['REFERENCED_TABLE_NAME'],
                    'foriegn_key' => $tableInfo['REFERENCED_COLUMN_NAME']
                ];
            }
        }
        return [
           'primary_keys' => $general_info_PK,
           'foriegn_keys' => $general_info_FK,
            ];
    }

    public function showColumns($table){
        $tables_arr = [];
        $info = $this->DB->query("
                        SHOW TABLES FROM ".$table
        );
        $info->setFetchMode(PDO::FETCH_ASSOC);
        $result = $info->fetchAll();
        foreach ($result as $tableName){
            $tables_arr[] = $this->camelCaseClass($tableName['Tables_in_bankexample']);
        }
        return $tables_arr;
    }

    public function createEntity($tables){
        $len = count($tables);

        for ($i=0; $i<$len; $i++ ){
            file_put_contents ("Data\\Entity\\".ucfirst($tables[$i]) .".php", "<?php 

namespace Data\\Entity;
use Core\Model\EntityModel;


class ". ucfirst($tables[$i]) ." extends EntityModel{

        protected \$tableName = '$tables[$i]';
         protected \$visible = [''];

        
}
?>"
            );
        }
    }

    public function createRepo($tables){
        $loader_JSON = BASE_DIR . '\DependencyInjection_config.json';
        $serviceLoader = json_decode(file_get_contents($loader_JSON), true);

        $len = count($tables);

        for ($i=0; $i<$len; $i++ ){
            file_put_contents ("Data\\Repository\\Abst\\I".ucfirst($tables[$i]) ."Repo.php", "<?php 

namespace Data\\Repository\\Abst;

Interface I". ucfirst($tables[$i]) ."Repo {

    public function save();

    public function bulkInsert(\$arr);

    public function bulkInsertReturnId(\$arr);

    public function update(\$arr, \$id);

    public function find(\$id);

    public function with(...\$relations);

    public function where(...\$parameters);

    public function orderBy(\$feild, \$orderType = null);

    public function groupBy(\$feild);

    public function having(\$feild, \$agregateFunction, \$operator, \$amount);

    public function take(\$amount);

    public function get();

    public function search(\$inWhere, \$searchArg);

    public function delete();

    public function first();

        
        
}
?>"
            );

            file_put_contents ("Data\\Repository\\Concrete\\".ucfirst($tables[$i]) ."Repo.php", "<?php 

namespace Data\\Repository\\Concrete;
use Data\Entity\\".$tables[$i].";
use Data\\Repository\\Abst\\I".$tables[$i]."Repo;


class ". ucfirst($tables[$i]) ."Repo implements I". $tables[$i] ."Repo {

        public function where(\$in, \$how, \$opt = null)
    {
        return ".$tables[$i]."::where(\$in, \$how, \$opt = null);
    }

    public function first()
    {
        return ".$tables[$i]."::first();
    }

    public function orWhere(\$in, \$like, \$val)
    {
        return ".$tables[$i]."::orWhere(\$in, \$like, \$val);
    }

    public function get()
    {
        return ".$tables[$i]."::get();
    }

    public function with(...\$val)
    {
        return ".$tables[$i]."::with(...\$val);
    }

    public function insert(\$var)
    {
        return ".$tables[$i]."::insert(\$var);
    }

    public function delete()
    {
        return ".$tables[$i]."::delete();
    }

    public function all()
    {
        return ".$tables[$i]."::all();
    }
}
?>"
            );
            $repo_interface = 'Data\\Repository\\Concrete\\I'.$tables[$i].'Repo';
            $repo_impl = 'Data\\Repository\\Concrete\\'.$tables[$i].'Repo';

            if (!isset($serviceLoader['kernel']['services'][$repo_interface])){
                $serviceLoader['kernel']['services'][$repo_interface] = $repo_impl;
            };
        }
        file_put_contents($loader_JSON, json_encode($serviceLoader));
    }

    public function camelCaseClass($str){
       $strlen = strlen($str);
        for ($i=0; $i<$strlen; $i++){
           $str[$i] == '_' ? $str[$i+1] = strtoupper($str[$i+1]): null;
        }
        if ($str[$strlen-1] == 's'){
            if ($str[$strlen-3] == 's'){
                $str = substr($str, 0, -2);
            } else {
                $str =  substr($str, 0, -1);
            }
        }
        $str = ucfirst(str_replace('_','',$str));


        return $str;
    }

    public function extendableClasses($tables){

    }
}
