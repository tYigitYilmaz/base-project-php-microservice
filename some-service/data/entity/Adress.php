<?php 

namespace Data\Entity;
use Core\Model\EntityModel;


class Adress extends EntityModel {

        protected $tableName = 'Adresses';
        protected $visible = ['customer_id', 'adress'];

}
?>