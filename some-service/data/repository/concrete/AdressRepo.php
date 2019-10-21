<?php 

namespace Data\Repository\Concrete;
use Data\Entity\Adress;
use Data\Repository\Abst\IAdressRepo;


class AdressRepo implements IAdressRepo {

    public function save()
    {
        return Adress::save();
    }

    public function bulkInsert($arr)
    {
        return Adress::bulkInsert();
    }

    public function bulkInsertReturnId($arr)
    {
        return Adress::bulkInsertReturnId($arr);
    }

    public function update($arr, $id)
    {
        return Adress::update($arr, $id);
    }

    public function find($id)
    {
        return Adress::find($id);
    }

    public function with(...$relations)
    {
        return Adress::with(...$relations);
    }

    public function where(...$parameters)
    {
        return Adress::where(...$parameters);
    }

    public function orderBy($feild, $orderType = null)
    {
        return Adress::orderBy($feild, $orderType = null);
    }

    public function groupBy($feild)
    {
        return Adress::groupBy($feild);
    }

    public function having($feild, $agregateFunction, $operator, $amount)
    {
        return Adress::having($feild, $agregateFunction, $operator, $amount);
    }

    public function take($amount)
    {
        return Adress::take($amount);
    }

    public function get()
    {
        return Adress::get();
    }

    public function search($inWhere, $searchArg)
    {
        return Adress::search($inWhere, $searchArg);
    }

    public function delete()
    {
        return Adress::delete();
    }

    public function first()
    {
        return Adress::first();
    }
    public function all()
    {
        return Adress::all();
    }
}
?>