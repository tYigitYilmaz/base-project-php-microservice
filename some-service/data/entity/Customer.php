<?php 

namespace Data\Entity;
use Core\Model\EntityModel;


class Customer extends EntityModel{

        protected $tableName = 'Customers';
        protected $visible = ['customer_id', 'channel_id'];


    public function adress(){
        return $this->belongsTo('Data\Entity\Adress');
    }


}
?>