<?php
namespace Lubed\MVCKernel;

interface ViewResolver {
    public function resolve(View $view);
}