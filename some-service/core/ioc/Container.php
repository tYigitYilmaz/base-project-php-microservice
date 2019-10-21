<?php 

namespace Core\IOC;

use ReflectionClass;

class Container {

    private $services = [];

    public function addService(
        string $name,
        \Closure $closure

    ): void
    {
        $this->services[$name] = $closure;
    }

    public function hasService(string $name): bool
    {
        return isset($this->services[$name]);
    }

    public function getService(string $name)
    {
        if (!$this->hasService($name)) {
            return null;
        }
        if ($this->services[$name] instanceof \Closure) {
            $this->services[$name] = $this->services[$name]();
        }
        return $this->services[$name];
    }

    public function getServices(): array
    {
        return [
            'services' => array_keys($this->services)
        ];
    }

    public function loadServices(string $namespace, ?\Closure $callback = null): void
    {
        $actualDirectory = BASE_DIR. '/' .$namespace;
        $files = array_filter(scandir($actualDirectory), function ($file) {
            return $file !== '.' && $file !== '..';
        });
        foreach($files as $file) {

            if (preg_match("/.php/", $file)){

                $class = new ReflectionClass(
                    $namespace . '\\' . basename($file, '.php')
                );
                $serviceName = $class->getName();
                $constructor = $class->getConstructor();

                if (!$constructor)
                    continue;
                $arguments = $constructor->getParameters();

                $serviceParameters = [];

                foreach ($arguments as $argument) {
                    $type = (string)$argument->getType();

                    if ($this->hasService($type)) {
                        $serviceParameters[] = $this->getService($type);
                    } else {
                        $serviceParameters[] = function() use ($type) {
                            return $this->getService($type);
                        };
                    }
                }

                $this->addService($serviceName, function () use ($serviceName, $serviceParameters) {
                    foreach ($serviceParameters as &$serviceParameter) {
                        if ($serviceParameter instanceof \Closure) {
                            $serviceParameter = $serviceParameter();
                        }
                    }
                    return new $serviceName(...$serviceParameters);
                });

                if ($callback) {
                    $callback($serviceName, $class);
                }
            }
        }
    }
}


?>