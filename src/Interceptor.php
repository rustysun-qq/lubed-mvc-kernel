<?php
namespace Lubed\MVCKernel;

use Lubed\Supports\Action;

interface Interceptor
{
    public function beforeIntercept(Action $action, $handler);
    public function afterIntercept(Action $action, $handler);
}
