<?php
namespace App\Controller;

use Data\Entity\Adress;
use Data\Entity\Customer;
use Data\Repository\Abst\IAdressRepo;
use Data\Repository\Abst\ICustomerRepo;
use Service\Abst\IGenerateService;
use Service\Gateway\IReceiverService;


class GeneratorController
{
    private $iGenerateService;
    private $iReceiverService;
    private $iAdressRepo;

    public function __construct(IGenerateService $iGenerateService, IReceiverService $iReceiverService, ICustomerRepo $iAdressRepo)
    {
        $this->iAdressRepo = $iAdressRepo;
        $this->iGenerateService = $iGenerateService;
        $this->iReceiverService = $iReceiverService;
    }

    public function generate()
    {
       $this->iGenerateService->generate();
    }
    public function save()
    {
        $data = new Adress();
        $data->adress = 'asfas';
        $data->save();
    }

    public function bulkInsert()
    {
        /*$data = [
            ['customer_id' => 1, 'channel_id' => 3],
            ['customer_id' => 2, 'channel_id' => 3],
            ['customer_id' => 3, 'channel_id' => 3],
            ['customer_id' => 4, 'channel_id' => 3],
        ];*/
        $data = [
            ['customer_id' => 1, 'adress' => 'sdfgasdg'],
            ['customer_id' => 2, 'adress' => 'sdfgasdg'],
        ];
        $this->iAdressRepo->bulkInsert($data);
    }

    public function bulkInsertReturnId()
    {
        $data = [
            ['customerNo' => 21, 'adress' => 'sdfgasdg'],
            ['customerNo' => 24, 'adress' => 'sdfgasdg'],
        ];
        $this->iAdressRepo->bulkInsertReturnId($data);
    }

    public function update()
    {
        $data = new Customer();
        $data->customerNo = 3;
        $data->channelId = 4;

        $data->update($data, 1);
    }

    public function get()
    {
        $this->iAdressRepo->where('a1','a2','<')->where('b1','b2','<','b3','b4','like')->where('c1','c2','<')->orderBy('column', 'des')->having('column','AVG','>',5)->where('a','sd','<','a','ssd','like')->get();
//        dd($this->iAdressRepo->where('customerNo',3,'like')->where('channelId',4,'=')->get());
//        $this->iAdressRepo->with('adress');
//        dd($this->iAdressRepo->search('customerNo','sdf','like')->get());
//        dd($this->iAdressRepo->where('id',1)->delete());

    }
}