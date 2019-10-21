<?php 

namespace Core\IOC;

class Kernel {

    private $container;

    public function __construct()
    {
        $this->container = new Container();
    }

    public function getContainer(): Container
    {
        return $this->container;
    }

    public function boot()
    {
        $this->bootContainer($this->container);

        return $this;
    }

    private function bootContainer(Container $container) 
    {
        $loader_JSON = BASE_DIR . '\DependencyInjection_config.json';
        $serviceLoader = json_decode(file_get_contents($loader_JSON));

        if (!is_null($serviceLoader->kernel->services)){
            foreach ($serviceLoader->kernel->services as $called => $injected) {
                $container->addService($called, function() use ($container, $injected) {
                    if ( $container->hasService($injected) ) return $container->getService($injected);
                    else return new $injected();
                });
            }
        }

        if (!is_null($serviceLoader->kernel->loadServices)){
            foreach ($serviceLoader->kernel->loadServices as $injected) {
                $container->loadServices($injected);
            }
        }
    }
}


?>