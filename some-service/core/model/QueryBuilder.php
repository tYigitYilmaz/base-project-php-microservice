<?php


namespace Core\Model;


 use Exception;

 class QueryBuilder
{

     protected static $operators = [
         '=', '<', '>', '<=', '>=', '!<', '!>', '<>', '!=',
         'like', 'not like', 'ilike',
         '&', '&=', '|', '|=', '^', '^=',
     ];
     /**
      * @var string
      */

     protected static $whereBlog;
     protected static $groupByItem;
     protected static $havingBlog;
     protected static $orderString;
     protected static $limitAmount;


     public static function callerClassProvider()
     {
         $ex = new Exception();
         $trace = $ex->getTrace();
         $final_call = $trace;
         $lenTrace = count($trace);

         for ($i = 0; $i < $lenTrace; $i++){
             $trigger_entity= explode('\\',$final_call[$i]['class']);
             $len = count($trigger_entity);
             if (preg_match('/repo/i',$trigger_entity[$len-1])){
                 $tableName = $trigger_entity[$len-1];
                 break;
             }
         }
         $tableName = str_replace('Repo', '',$tableName);
         return "Data\\Entity\\".$tableName;
     }

     public static function protectedPropertyProvider($class, $property)
     {
         $entity = new \ReflectionClass($class);

         $reflectionProperty = $entity->getProperty($property);
         $reflectionProperty->setAccessible(true);
         return $reflectionProperty->getValue(new $class);
     }

     public static function invokeMethod($class, ...$methods)
     {
         $relations = array();

        foreach ($methods as $method) {

            $reflectionMethod = new \ReflectionMethod($class, $method);
            $relations[ucfirst($method)] = $reflectionMethod->invoke(new $class());
        }
        return $relations;
     }

     public static function wrapper($data)
     {
         $data = self::arrayAble($data);

         return [
             'columns' => self::queryColumnWrap($data),
             'values' => self::queryValueWrap($data)
         ];
     }

     public static function propertyEqualsValue($data)
     {
         $data = self::arrayAble($data);

         $columns = array_keys($data);
         $queryString = '';

         foreach ($columns as $column) {
             $queryString .= $column.' = '.$data[$column].', ';
         }
         return mb_substr($queryString, 0, -2);
     }

     public static function arrayAble($data)
     {
         switch (gettype($data)) {
             case 'array':

        break;
             case 'object':
                 $data = json_decode(json_encode($data),true);
        break;
             case 'string':
                 $data = json_decode($data,true);
         }
        return $data;
     }

     public static function queryColumnWrap($data)
     {
         $columns = array_keys($data);
         $queryString = '';

         foreach ($columns as $value) {
             $queryString .= $value.', ';
         }
         return '('.mb_substr($queryString, 0, -2).')';
     }

     public static function queryValueWrap($data)
     {
         $queryString = '';

         foreach ($data as $value) {
             $queryString .= "'".$value."', ";
         }
         return '('.mb_substr($queryString, 0, -2).')';
     }

     public static function compileInsertQuery($data, $tableName)
     {
         $queries = self::wrapper($data);
         $columns = $queries['columns'];
         $values = $queries['values'];

         return 'INSERT INTO '.$tableName.' '.$columns.' VALUES '.$values;
     }

     public static function compileBulkInsertQuery($sqlColumnsList, $insertArray, $tableName)
     {
         $file = "";
         $chunkArrayLength = count($sqlColumnsList);

         $columns = str_replace('\'','',self::queryValueWrap($sqlColumnsList));

         foreach ($insertArray as $insertData){
             $str ="";
           foreach ($sqlColumnsList as $key=>$sqlColumn){
               $str .= '\''.$insertData[$sqlColumn].'\',';
               if ($key == $chunkArrayLength - 1){
                   $file .= '('.mb_substr($str, 0, -1).'),';
               }
           }
        }
         $file = mb_substr($file, 0, -1);
         return "INSERT INTO ".$tableName." ".$columns." VALUES ".$file;
     }

     public static function compileUpdateQuery($data, $tableName, $id)
     {

         $equals = self::propertyEqualsValue($data);

         return 'UPDATE '.$tableName.' SET '.$equals.' where '.' id = '.$id;
     }

     public static function getTableColumns($tableName)
     {
         return "DESCRIBE ".$tableName;
     }

     public static function compileLastInsertId($tableName)
     {
         return "SELECT id FROM ".$tableName." ORDER BY id DESC LIMIT 1;";
     }

     public static function nestedArrayCollector($arr,...$fields)
     {
         $collection = array();
         foreach ($arr as $value){
            foreach ($fields as $field){
                !$value['Extra'] == 'auto_increment' ?  $collection[] = $value[$field] : null;
            }
         }
         return $collection;
     }

     public static function prepareFindString($id, $tableName)
     {
        return "SELECT * FROM ".$tableName." WHERE id = $id;";
     }

     public static function prepareWhereString($parameters)
     {
         $whereString = '';
         $stepStr = ' = ';
         $operatorAdded = false;
         foreach ($parameters as $parameter){
             if(explode(" = ",$stepStr)[0] != '' && explode(" = ",$stepStr)[1] != '' && !in_array($parameter,self::$operators)){
                 $operatorAdded = true;
             }

             if ($stepStr == ' = '&& !in_array($parameter,self::$operators)) {
                 $stepStr = $parameter.$stepStr;
             } elseif (!in_array($parameter,self::$operators)) {
                 $stepStr = $stepStr.$parameter;
             }
             if (in_array($parameter,self::$operators)){
                 $stepStr = str_replace('=', $parameter, $stepStr);
                 $operatorAdded = true;
             }
             if ($operatorAdded == true) {
                 $whereString .= $stepStr.' AND ';
                 $stepStr = ' = ';
                 $operatorAdded = false;
             }
         }

         self::$whereBlog .= '@'.mb_substr($whereString, 0, -5).'@^';
     }

     public static function prepareOrderString($feild, $orderType = null, $agregateFunction = null, $operator = null, $amount = null)
     {
            if (!is_null($agregateFunction) && !is_null($operator) && !is_null($amount)) {
                self::$orderString .= " HAVING ".strtoupper($agregateFunction).'('.$feild.')'."$operator $amount";
            } else {
                $orderType = ' '.strtoupper($orderType) ?? null ;
                self::$orderString = " ORDER BY $feild $orderType";
            }
     }

     public static function prepareGroupString($feild)
     {
         self::$groupByItem = " GROUP BY $feild";
     }

     public static function prepareHavingString($feild, $agregateFunction, $operator, $amount)
     {
         self::$havingBlog .= " HAVING ".strtoupper($agregateFunction)."($feild) $operator $amount";
     }

     public static function prepareLimitString($amount)
     {
         self::$limitAmount .= " LIMIT $amount";
     }

     public static function opearationGet($tableName)
     {
         $whereString = '';
         $orderString = '';
         $havingBlog = '';
         $groupByItem = '';
         $limitAmount = '';

         if (!is_null(self::$whereBlog))
         {
             $whereString = self::$whereBlog;
             $whereString = 'WHERE '.mb_substr($whereString, 1, strlen($whereString)-1);
             $whereString = str_replace('@^@',' AND ', $whereString);
             $whereString = str_replace('@^','', $whereString);
             self::unloadStaticField(self::$whereBlog);
         }

         if (!is_null(self::$orderString))
         {
             $orderString = self::$orderString;
             self::unloadStaticField(self::$orderString);
         }

         if (!is_null(self::$havingBlog))
         {
             $havingBlog = self::$havingBlog;
             self::unloadStaticField(self::$havingBlog);
         }

         if (!is_null(self::$groupByItem))
         {
             $groupByItem = self::$groupByItem;
             self::unloadStaticField(self::$groupByItem);
         }

         if (!is_null(self::$limitAmount))
         {
             $limitAmount = self::$limitAmount;
             self::unloadStaticField(self::$limitAmount);
         }

         return "SELECT * FROM $tableName ".$whereString . $groupByItem . $havingBlog . $orderString . $limitAmount.';';
     }

     public static function opearationFirst($tableName)
     {
         $whereString = '';
         $orderString = '';
         $havingBlog = '';
         $groupByItem = '';
         self::prepareLimitString(1);

         if (!is_null(self::$whereBlog))
         {
             $whereString = self::$whereBlog;
             $whereString = 'WHERE '.mb_substr($whereString, 1, strlen($whereString)-1);
             $whereString = str_replace('@^@',' AND ', $whereString);
             $whereString = str_replace('@^','', $whereString);
             self::unloadStaticField(self::$whereBlog);
         }

         if (!is_null(self::$orderString))
         {
             $orderString = self::$orderString;
             self::unloadStaticField(self::$orderString);
         }

         if (!is_null(self::$havingBlog))
         {
             $havingBlog = self::$havingBlog;
             self::unloadStaticField(self::$havingBlog);
         }

         if (!is_null(self::$groupByItem))
         {
             $groupByItem = self::$groupByItem;
             self::unloadStaticField(self::$groupByItem);
         }

         if (!is_null(self::$limitAmount))
         {
             $limitAmount = self::$limitAmount;
             self::unloadStaticField(self::$limitAmount);
         }
         return "SELECT * FROM $tableName ".$whereString . $groupByItem . $havingBlog . $orderString . $limitAmount.';';
     }

     public static function opearationDelete($tableName)
     {
         $whereString = '';

         if (!is_null(self::$whereBlog))
         {
             $whereString = self::$whereBlog;
             $whereString = 'WHERE '.mb_substr($whereString, 1, strlen($whereString)-1);
             $whereString = str_replace('@^@',' AND ', $whereString);
             $whereString = str_replace('@^','', $whereString);
             self::unloadStaticField(self::$whereBlog);
         }

         return "DELETE * FROM $tableName ".$whereString.';';
     }

     public function queryJoinedWithVisibleFields($tablesWithRelations)
     {
         $selectString = '';
         foreach ($tablesWithRelations as $relation){
             $visibles = self::protectedPropertyProvider($relation['entity'], 'visible');
             foreach ($visibles as $visible){
                 $selectString .= $relation['tableName'].".$visible, ";
                }
             $table[] = ['tableName' => $relation['tableName'], 'foreignKey' => $relation['foreignKey']];
         }

         $selectString = mb_substr($selectString, 0, -2);
         $tableLeft = $table[0]['tableName'];
         $tableRight = $table[1]['tableName'];

         $foreignKeyLeft = $table[0]['foreignKey'];
         $foreignKeyRight = $table[1]['foreignKey'];

         return "SELECT $selectString FROM $tableLeft AS $tableLeft
                   JOIN 
                   $tableRight AS $tableRight ON $tableLeft.$foreignKeyLeft = $tableRight.$foreignKeyRight";
     }

     public function belongsTo($related, $foreignKey=null)
     {
         return [
            'entity' => $related,
            'tableName' => self::protectedPropertyProvider($related, 'tableName'),
            'foreignKey' => $foreignKey,
         ];
     }


     public static function unloadStaticField(&$staticField)
     {
        return $staticField = null;
     }
}