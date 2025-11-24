<?php

class NewRouter
{
    private $configFactory;
    private $defaultController;
    private $defaultMethod;

    public function __construct($configFactory, $defaultController, $defaultMethod)
    {
        $this->configFactory = $configFactory;
        $this->defaultController = $defaultController;
        $this->defaultMethod = $defaultMethod;
    }

    public function executeController($controllerParam = null, $methodParam = null)
    {

        if ($controllerParam === 'index.php' || empty($controllerParam)) {
            $controllerParam = $this->defaultController;
        }

        $controller = $this->getControllerFrom($controllerParam);
        $this->executeMethodFromController($controller, $methodParam);
    }

    private function getControllerFrom($controllerName)
    {
        $controllerName = $this->getControllerName($controllerName);
        $controller = $this->configFactory->get($controllerName);

        if ($controller === null) {
            $controller = $this->configFactory->get($this->defaultController);
        }

        return $controller;
    }

    private function executeMethodFromController($controller, $methodName)
    {
        // Determine target method: explicit if exists, else default only if default exists on that controller.
        $method = $this->getMethodName($controller, $methodName);
        if (!method_exists($controller, $method)) {
            // If requested method invalid and default also missing, abort with clear error instead of calling unrelated default.
            if (!method_exists($controller, $this->defaultMethod)) {
                throw new RuntimeException('Método "' . $methodName . '" no encontrado en ' . get_class($controller) . ' y el método por defecto "' . $this->defaultMethod . '" tampoco existe.');
            }
            $method = $this->defaultMethod;
        }
        call_user_func([$controller, $method]);
    }

    private function getControllerName($controllerName)
    {
        if (!empty($controllerName)) {
            if (substr($controllerName, -10) === 'Controller') {
                return $controllerName;
            }
            return ucfirst(strtolower($controllerName)) . 'Controller';
        }
        return $this->defaultController;
    }

    private function getMethodName($controller, $methodName)
    {
        if (!empty($methodName)) {
            return $methodName; // devolver explícito; validación después
        }
        return $this->defaultMethod;
    }
}