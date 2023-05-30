<?php
namespace Lubed\MVCKernel;

use Lubed\Supports\Kernel;

final class DefaultKernel implements Kernel
{
	public function __construct() {
	}

	public function boot(&$result)
	{
        if (!method_exists($controller, $action_handler)) {
            MVCExceptions::invalidActionHandler(sprintf('%s.%s:No valid action handler found:%s ',__CLASS__,__METHOD__,$action_handler));
        }

        $interceptors = $this->dispatcher_info->interceptors;
        $passed = true;
        $result = NULL;
        //Before Controller->action() call
        foreach ($interceptors as $interceptor) {
            $result = $interceptor->beforeIntercept($action, $controller);

            if ($result instanceof View) {
                return $result;
            }

            if (false === $result) {
                $passed = false;
                break;
            }
        }

        if ($passed) {
            return $result;
        }

        $result=$this->invokeAction($controller, $actionHandler, $action->getArguments());

        //After Controller->action() call
        foreach ($interceptors as $interceptor) {
            $interceptor->afterIntercept($action, $controller);
        }
        return $result;
	}

    private function invokeAction($object, string $method, array $arguments) {
        $ctl=get_class($object);
        $methodInfo=$this->reflection_factory->getMethod($ctl, $method);
        $parameters=$methodInfo->getParameters();
        $values=[];
        $total=count($parameters);

        for ($i=0; $i < $total; $i++) {
            $parameter=array_shift($parameters);
            $name=$parameter->getName();
            if (isset($arguments[$name])) {
                $values[] = $arguments[$name];
            } else if ($parameter->isOptional()) {
                $values[] = $parameter->getDefaultValue();
            } else {
                MVCExceptions::missRequiredArgument(
                    vsprintf("%s.%s:Missing required argument: %s for action %s:%s",[
                        __CLASS__, __METHOD__, $name, $ctl, $method
                    ])
                );
            }
        }

        return $methodInfo->invokeArgs($object, $values);
    }
}
