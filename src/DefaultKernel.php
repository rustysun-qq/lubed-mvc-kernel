<?php
namespace Lubed\MVCKernel;

use Lubed\Router\Routing\RoutingDestination;
use Lubed\Supports\Kernel;
use Lubed\Reflections\{ReflectionFactory, ReflectionFactoryAware, DefaultReflectionFactory};

final class DefaultKernel implements Kernel, ReflectionFactoryAware {
    private $is_init;
    private $controller;
    private $action;
    private $interceptors;
    private $reflection_factory;
    private $request;
    private $parameters;
    private $view;

    public function __construct() {
        $this->is_init=false;
        $this->setReflectionFactory(new DefaultReflectionFactory);
        $this->interceptors=[];
    }

    public function getRequest() {
        return $this->request;
    }

    public function setRequest($request) : self {
        $this->request=$request;
        return $this;
    }

    public function init(RoutingDestination $destination) {
        $rdi=$destination->getRDI();
        $this->controller=$rdi->getController();
        $this->action=$rdi->getAction();
        $parameters=$destination->getParameters();
        if (!method_exists($this->controller, $this->action)) {
            MVCExceptions::invalidActionHandler(sprintf('%s:No valid action handler found:%s ', __METHOD__, $this->action));
        }
        if ($parameters) {
            $this->parameters=$parameters;
        }
        $this->is_init=true;
    }

    public function boot(&$result) {
        if (!$this->is_init) {
            MVCExceptions::invalidActionHandler('Kernel before boot must init it first.');
        }
        foreach ($this->interceptors as $interceptor) {
            if (!$interceptor instanceof HttpInterceptor) {
                continue;
            }
            if ($interceptor->intercept()) {
                return false;
            }
        }
        $parameters=$this->parameters ? $this->parameters : [];
        $result=$this->invokeAction($this->controller, $this->action, $parameters);
        return true;
    }

    public function setReflectionFactory(ReflectionFactory $reflection_factory) {
        $this->reflection_factory=$reflection_factory;
    }

    public function registerView($view) {
        $this->view=$view;
    }

    private function invokeAction($ctl, string $method, array $arguments) {
        if (!$ctl || !$method) {
            MVCExceptions::invalidActionHandler("Invalid method(%s->%s).", $ctl, $method);
        }
        $rf_method=$this->reflection_factory->getMethod($ctl, $method);
        $parameters=$rf_method->getParameters();
        $values=[];
        $total=count($parameters);
        for ($i=0; $i < $total; $i++) {
            $parameter=array_shift($parameters);
            $name=$parameter->getName();
            if (isset($arguments[$name])) {
                $values[]=$arguments[$name];
            } else if (isset($arguments[$i])) {
                $values[]=$arguments[$i];
            } else if ($parameter->isOptional()) {
                $values[]=$parameter->getDefaultValue();
            } else {
                MVCExceptions::missRequiredArgument(sprintf("Missing required argument: %s for action %s:%s", $name, $ctl, $method), ['method'=>__METHOD__]);
            }
        }
        //TODO:.....
        $controller=new $ctl($this->request);
        $controller->init();
        $controller->setView($this->view);
        return $rf_method->invokeArgs($controller, $values);
    }
}
