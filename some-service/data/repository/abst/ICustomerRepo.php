<?php 

namespace Data\Repository\Abst;

Interface ICustomerRepo
{

    public function save();

    public function bulkInsert($arr);

    public function bulkInsertReturnId($arr);

    public function update($arr, $id);

    public function find($id);

    public function with(...$relations);

    public function where(...$parameters);

    public function orderBy($feild, $orderType = null);

    public function groupBy($feild);

    public function having($feild, $agregateFunction, $operator, $amount);

    public function take($amount);

    public function get();

    public function search($inWhere, $searchArg);

    public function delete();

    public function first();

}
?>