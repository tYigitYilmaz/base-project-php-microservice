<?php

namespace Core\Model;


use Core\DBGenerator;
use Data\Repository\Abst\IAdressRepo;
use PDO;

abstract class EntityModel extends QueryBuilder

{
    private static $DB;
    private static $channedRequestTableName;

    public function __construct()
    {
        try {
            self::$DB = DBGenerator::connectDB();

        } catch (PDOException $ex) {
            // log connection error for troubleshooting and return a json error response
            error_log("Connection Error: " . $ex, 0);
            $response = new Response(false, ['Database connection error'], $ex, 500);
            $response->send();
            exit;
        }
    }

    public function save()
    {
       $query = self::compileInsertQuery($this->arrayAble($this), $this->tableName);
       self::$DB->query($query);
       return true;
    }

    public function bulkInsert($arr)
    {
        $tableName = self::protectedPropertyProvider(self::callerClassProvider(),'tableName');
        $tableColumns = self::$DB->query(self::getTableColumns($tableName));
        $tableColumnInfo = $tableColumns->fetchAll();

        $columns = self::nestedArrayCollector($tableColumnInfo,'Field');

        $query = self::compileBulkInsertQuery($columns, $arr, $tableName);
        self::$DB->query($query);
    }

    public function bulkInsertReturnId($arr)
    {
        $tableName = self::protectedPropertyProvider(self::callerClassProvider(),'tableName');
        $tableColumns = self::$DB->query(self::getTableColumns($tableName));
        $tableColumnInfo = $tableColumns->fetchAll();

        $columns = self::nestedArrayCollector($tableColumnInfo,'Field');

        $query = self::compileBulkInsertQuery($columns, $arr, $tableName);
        self::$DB->query($query);

        $query = self::compileLastInsertId($tableName);
        $id = self::$DB->query($query);
        $result = $id->fetchAll(PDO::FETCH_ASSOC);
        return $result;
    }

    public function update($arr, $id)
    {
        $query = self::compileUpdateQuery($arr, $this->tableName, $id);
        self::$DB->query($query);
        return true;
    }

    public function find($id)
    {
        $tableName = self::protectedPropertyProvider(self::callerClassProvider(),'tableName');
        $query = self::prepareFindString($id, $tableName);
        $item = self::$DB->query($query);
        $result = $item->fetchAll(PDO::FETCH_ASSOC);
        return $result;
    }
    public function with(...$relations)
    {
        $callerClass = self::callerClassProvider();
        $tablesWithRelations = self::foreignKeyProvider(self::invokeMethod($callerClass, ...$relations));
        $query = self::queryJoinedWithVisibleFields($tablesWithRelations);

        $result = self::$DB->query($query);
        $result = $result->fetchAll(PDO::FETCH_ASSOC);

        return $result;
    }

    public function where(...$parameters)
    {
        self::$channedRequestTableName ?? self::$channedRequestTableName = (self::protectedPropertyProvider(self::callerClassProvider(),'tableName'));
        self::prepareWhereString($parameters);

        return new static;
    }

    public function orderBy($feild, $orderType = null)
    {
        self::$channedRequestTableName ?? self::$channedRequestTableName = (self::protectedPropertyProvider(self::callerClassProvider(),'tableName'));
        self::prepareOrderString($feild, $orderType);
        return new static;
    }

    public function groupBy($feild)
    {
        self::$channedRequestTableName ?? self::$channedRequestTableName = (self::protectedPropertyProvider(self::callerClassProvider(),'tableName'));
        self::prepareGroupString($feild);
        return new static;
    }

    public function having($feild, $agregateFunction, $operator, $amount)
    {
        self::$channedRequestTableName ?? self::$channedRequestTableName = (self::protectedPropertyProvider(self::callerClassProvider(),'tableName'));
        self::prepareHavingString($feild, $agregateFunction, $operator, $amount);
        return new static;
    }

    public function take($amount)
    {
        self::$channedRequestTableName ?? self::$channedRequestTableName = (self::protectedPropertyProvider(self::callerClassProvider(),'tableName'));
        self::prepareLimitString($amount);
        return new static;
    }

    public function get()
    {
        $tableName = self::$channedRequestTableName;
        $query = self::opearationGet($tableName);
        self::unloadStaticField(self::$channedRequestTableName);
dd($query);
        $item = self::$DB->query($query);
        $result = $item->fetchAll(PDO::FETCH_ASSOC);
        return $result;
    }

    public function search($inWhere, $searchArg)
    {
        self::$channedRequestTableName ?? self::$channedRequestTableName = (self::protectedPropertyProvider(self::callerClassProvider(),'tableName'));
        $searchArg = $searchArg.'%';
        $parameters = "$inWhere $searchArg like";
        $parameters = explode(' ', $parameters);

        self::prepareWhereString($parameters);
        return new static;
    }

    public function delete()
    {
        $tableName = self::$channedRequestTableName;
        $query = self::opearationDelete($tableName);
        return self::$DB->query($query);
    }

    public function first()
    {
        $tableName = self::$channedRequestTableName;
        $query = self::opearationFirst($tableName);
        self::unloadStaticField(self::$channedRequestTableName);

        $item = self::$DB->query($query);
        $result = $item->fetchAll(PDO::FETCH_ASSOC);
        return $result;
    }

    public function tableColumnsArrayArranger($tableName)
    {
        $columns = self::getTableColumns($tableName);
        $result = self::$DB->query($columns);
        $result = $result->fetchAll(PDO::FETCH_ASSOC);

        return self::nestedArrayCollector($result, 'Field');
    }

    public static function foreignKeyProvider(array $invokeMethod)
    {
        foreach ($invokeMethod as $key=>$result){
            if ($result['foreignKey'] == null) {
                $callerClass = self::callerClassProvider();
                $tableName = self::protectedPropertyProvider($callerClass,'tableName');

                $tableFields = self::tableColumnsArrayArranger($tableName);
                $tableFieldsRelatedTable = self::tableColumnsArrayArranger($result['tableName']);

                $callerClassName =explode('\\', $callerClass);
                $parentEntity = end($callerClassName);

                foreach ($tableFieldsRelatedTable as $tableField){
                    if (preg_match("/^.*$parentEntity.*\$/mi", $tableField) &&  preg_match("/^.*id.*\$/mi", $tableField)){
                        $invokeMethod[$key]['foreignKey'] = $tableField;
                        if (in_array($tableField, $tableFields)){
                            $invokeMethod[$parentEntity]['entity'] = $callerClass;
                            $invokeMethod[$parentEntity]['tableName'] = $tableName;
                            $invokeMethod[$parentEntity]['foreignKey'] = $tableField;
                        } else {
                            $invokeMethod[$parentEntity]['entity'] = $callerClass;
                            $invokeMethod[$parentEntity]['tableName'] = 'id';
                            $invokeMethod[$parentEntity]['foreignKey'] = $tableField;
                        }
                    }
              }
            }
        }
        return $invokeMethod;
    }



}