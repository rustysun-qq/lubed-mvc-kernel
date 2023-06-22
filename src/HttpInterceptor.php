<?php
namespace Lubed\MVCKernel;
interface HttpInterceptor {
    public function intercepte() : bool;
}