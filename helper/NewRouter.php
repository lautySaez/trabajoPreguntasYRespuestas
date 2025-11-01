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
        $method = $this->getMethodName($controller, $methodName);

        if (!method_exists($controller, $method)) {
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
        if (!empty($methodName) && method_exists($controller, $methodName)) {
            return $methodName;
        }
        return $this->defaultMethod;
    }
}