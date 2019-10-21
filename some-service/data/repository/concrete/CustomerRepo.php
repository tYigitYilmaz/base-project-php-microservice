<?php 

namespace Data\Repository\Concrete;
use Data\Entity\Customer;
use Data\Repository\Abst\ICustomerRepo;


class CustomerRepo implements ICustomerRepo
{

    public function save()
    {
        return Customer::save();
    }

    public function bulkInsert($arr)
    {
        return Customer::bulkInsert();
    }

    public function bulkInsertReturnId($arr)
    {
        return Customer::bulkInsertReturnId($arr);
    }

    public function update($arr, $id)
    {
        return Customer::update($arr, $id);
    }

    public function find($id)
    {
        return Customer::find($id);
    }

    public function with(...$relations)
    {
        return Customer::with(...$relations);
    }

    public function where(...$parameters)
    {
        return Customer::where(...$parameters);
    }

    public function orderBy($feild, $orderType = null)
    {
        return Customer::orderBy($feild, $orderType = null);
    }

    public function groupBy($feild)
    {
        return Customer::groupBy($feild);
    }

    public function having($feild, $agregateFunction, $operator, $amount)
    {
        return Customer::having($feild, $agregateFunction, $operator, $amount);
    }

    public function take($amount)
    {
        return Customer::take($amount);
    }

    public function get()
    {
        return Customer::get();
    }

    public function search($inWhere, $searchArg)
    {
        return Customer::search($inWhere, $searchArg);
    }

    public function delete()
    {
        return Customer::delete();
    }

    public function first()
    {
        return Customer::first();
    }
    public function all()
    {
        return Customer::all();
    }
}
?>