<?php

namespace Service\Gateway;



Interface IReceiverService
{

    public function receiver($serviceName, $callback);
}